<?php
namespace LLS\Bundle\SQSBundle\Factory;

use LLS\Bundle\SQSBundle\Interfaces\QueueFactoryInterface;
use LLS\Bundle\SQSBundle\Interfaces\MessageFactoryInterface;
use LLS\Bundle\SQSBundle\Interfaces\SQSInterface;
use LLS\Bundle\SQSBundle\Model\Queue;

/**
 * Factory class for Queue model
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
class QueueFactory implements QueueFactoryInterface
{
    /**
     * @var MessageFactoryInterface
     */
    protected $messageFactory;

    /**
     * {@inheritDoc}
     */
    public function __construct(MessageFactoryInterface $messageFactory)
    {
        $this->messageFactory = $messageFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function create(SQSInterface $sqs, $name)
    {
        return new Queue($sqs, $this->getMessageFactory(), $name);
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
    public static function getNameFromUrl($url)
    {
        $exploded = explode('/', $url);

        return trim(array_pop($exploded));
    }
}
