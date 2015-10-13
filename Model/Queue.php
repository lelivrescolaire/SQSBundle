<?php

namespace LLS\Bundle\SQSBundle\Model;

use LLS\Bundle\SQSBundle\Interfaces\SQSInterface;
use LLS\Bundle\SQSBundle\Interfaces\MessageFactoryInterface;
use LLS\Bundle\SQSBundle\Interfaces\MessageInterface;
use LLS\Bundle\SQSBundle\Interfaces\QueueInterface;

/**
 * Queue Model
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
class Queue implements QueueInterface
{
    /**
     * @var SQSInterface
     */
    protected $sqs;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $arn;

    /**
     * {@inheritDoc}
     */
    public function __construct(SQSInterface $sqs, MessageFactoryInterface $messageFactory, $name)
    {
        $this->sqs            = $sqs;
        $this->messageFactory = $messageFactory;
        $this->name           = $name;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessageFactory()
    {
        return $this->messageFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getARN()
    {
        if (!$this->arn) {
            $this->arn  = $this->sqs->getClient()->getQueueArn($this->url);
        }

        return $this->arn;
    }

    /**
     * {@inheritDoc}
     */
    public function getSize()
    {
        $response = $this->getAttributes(array("ApproximateNumberOfMessages"));

        return (int) $response['ApproximateNumberOfMessages'];
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl()
    {
        if (!$this->url) {
            $this->url = trim($this->sqs->getClient()->getQueueUrl(array(
                'QueueName' => $this->getName(),
            ))->get('QueueUrl'));
        }

        return $this->url;
    }

    /**
     * {@inheritDoc}
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addPermission($label, array $awsAccountId, array $actionName)
    {
        $arguments = array(
            'QueueUrl'      => $this->getUrl(),
            'Label'         => $label,
            'AWSAccountIds' => $awsAccountId,
            'Actions'       => $actionName,

        );

        $this->sqs->getClient()->addPermission($arguments);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function removePermission($label)
    {
        $arguments = array(
            'QueueUrl'      => $this->getUrl(),
            'Label'         => $label

        );

        $this->sqs->getClient()->removePermission($arguments);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteMessage(MessageInterface $message)
    {
        $arguments = array(
            'QueueUrl'      => $this->getUrl(),
            'ReceiptHandle' => $message->getReceiptHandle()
        );

        $this->sqs->getClient()
            ->deleteMessage($arguments);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttributes(array $attributes)
    {
        $arguments = array(
            'QueueUrl'   => $this->getUrl(),
            'Attributes' => $attributes
        );

        $this->sqs->getClient()->setQueueAttributes($arguments);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributes(array $attributes = array())
    {
        $arguments = array(
            'QueueUrl'       => $this->getUrl(),
            'AttributeNames' => $attributes
        );

        $response = $this->sqs->getClient()
            ->getQueueAttributes($arguments);

        return $response->get('Attributes');
    }

    /**
     * {@inheritDoc}
     */
    public function fetchMessages($nb=1, array $opt = array())
    {
        $arguments = array(
            'QueueUrl'            => $this->getUrl(),
            'MaxNumberOfMessages' => $nb,
        );

        if ($opt) {
            $arguments = array_merge($opt, $arguments);
        }

        $response = $this->sqs->getClient()->receiveMessage($arguments);
        $service = $this;

        $messages = array();

        if (!is_null($response->get('Messages'))) {
            $messages = array_map(function ($definition) use ($service) {
                return $service->getMessageFactory()
                    ->create(trim($definition['Body']))
                    ->setId(trim($definition['MessageId']))
                    ->setReceipthandle(trim($definition['ReceiptHandle']));
            }, $response->get('Messages'));
        }

        return $messages;
    }

    /**
     * {@inheritDoc}
     */
    public function sendMessage(MessageInterface $message, $delay = 0)
    {
        $response = $this->sqs->getClient()
            ->sendMessage(array(
                'QueueUrl'     => $this->getUrl(),
                'MessageBody'  => $message->getBody(),
                'DelaySeconds' => $delay,
            ));

        $message->setId(trim($response->get('MessageId')));

        return $message;
    }

    /**
     * Get SQS Service
     *
     * @return SQSInterface
     */
    public function getSQS()
    {
        return $this->sqs;
    }
}
