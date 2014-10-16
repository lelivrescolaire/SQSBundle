<?php
namespace LLS\Bundle\SQSBundle\Interfaces;

use LLS\Bundle\SQSBundle\Interfaces\MessageFactoryInterface;
use LLS\Bundle\SQSBundle\Interfaces\SQSInterface;
use LLS\Bundle\SQSBundle\Interfaces\QueueInterface;

/**
 * Define SQS Queue Factory valid structure
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
interface QueueFactoryInterface
{
    /**
     * Constructor
     *
     * @param MessageFactoryInterface $messageFactory Message factory
     */
    public function __construct(MessageFactoryInterface $messageFactory);

    /**
     * Create a Queue
     *
     * @param SQSInterface $sqs  SQS Client
     * @param string       $name Queue name
     *
     * @return QueueInterface
     */
    public function create(SQSInterface $sqs, $name);

    /**
     * Get Message Factory
     *
     * @return MessageFactoryInterface
     */
    public function getMessageFactory();
}