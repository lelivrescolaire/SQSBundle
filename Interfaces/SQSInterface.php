<?php

namespace LLS\Bundle\SQSBundle\Interfaces;

use LLS\Bundle\AWSBundle\Interfaces\ServiceInterface;

/**
 * Define SQS Service valid structure
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
interface SQSInterface extends ServiceInterface
{
    /**
     * Get SQS Client
     *
     * @return Aws\Sqs\SqsClient
     */
    public function getClient();

    /**
     * Create a SQS Queue
     *
     * @param QueueInterface $queue Queue to create
     *
     * @return QueueInterface
     */
    public function createQueue(QueueInterface $queue);

    /**
     * List SQS Queues
     *
     * @param string $prefix Queues names prefix
     *
     * @return array<QueueInterface>
     */
    public function listQueues($prefix = null);

    /**
     * Retreive a SQS Queue
     *
     * @param string $queueName Queue name
     *
     * @return QueueInterface
     */
    public function getQueue($queueName);

    /**
     * Delete a SQS Queue
     *
     * @param QueueInterface $queue Queue to delete
     *
     * @return {$this}
     */
    public function deleteQueue(Queueinterface $queue);
}