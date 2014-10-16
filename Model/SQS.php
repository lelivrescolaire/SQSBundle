<?php

namespace LLS\Bundle\SQSBundle\Model;

use LLS\Bundle\AWSBundle\Interfaces\ClientFactoryInterface;
use LLS\Bundle\AWSBundle\Interfaces\IdentityInterface;
use LLS\Bundle\SQSBundle\Interfaces\SQSInterface;
use LLS\Bundle\SQSBundle\Interfaces\QueueInterface;
use LLS\Bundle\SQSBundle\Interfaces\QueueFactoryInterface;
use LLS\Bundle\AWSBundle\Model\AbstractService;

use LLS\Bundle\SQSBundle\Exception\SQSQueueNotExists;

/**
 * SQS Service Model
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
class SQS implements SQSInterface
{
    /**
     * @var \Aws\Sqs\SqsClient
     */
    protected $client;

    /**
     * @var QueueFactoryInterface
     */
    protected $queueFactory;

    /**
     * @var array
     */
    protected $queues = array(
        'name' => array(),
        'url'  => array(),
        'arn'  => array()
    );

    /**
     * {@inheritDoc}
     */
    public function __construct(IdentityInterface $identity, ClientFactoryInterface $clientFactory)
    {
        $this->client = $clientFactory->createClient('Sqs', $identity);
    }

    /**
     * {@inheritDoc}
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get Queue Factory
     *
     * @param QueueFactoryInterface $queueFactory Queue Factory
     *
     * @return {$this}
     */
    public function setQueueFactory(QueueFactoryInterface $queueFactory)
    {
        $this->queueFactory = $queueFactory;

        return $this;
    }

    /**
     * Get Queue Factory
     *
     * @return QueueFactoryInterface
     */
    public function getQueueFactory()
    {
        return $this->queueFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function createQueue(QueueInterface $queue)
    {
        $response = $this->getClient()->createQueue(array(
            "QueueName" => $queue->getName()
        ));

        $queue->setUrl(trim($response->get('QueueUrl')));

        return $queue;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteQueue(QueueInterface $queue)
    {
        $this->getClient()->deleteQueue(array(
            'QueueUrl' => $queue->getUrl()
        ));

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function listQueues($prefix = null)
    {
        $response = $this->getClient()->listQueues(array(
            'QueueNamePrefix' => ($prefix ?: '')
        ));
        $service  = $this;

        $queues = array_map(function ($queueUrl) use ($service) {
            $queueName = $service->getQueueFactory()->getNameFromUrl(trim($queueUrl));

            return $service->getQueue($queueName);
        }, $response->get('QueueUrls'));

        return $queues;
    }

    /**
     * {@inheritDoc}
     */
    public function getQueue($queueName)
    {
        return $this->getQueueFactory()->create($this, $queueName);
    }
}