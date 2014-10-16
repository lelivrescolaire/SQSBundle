<?php
namespace LLS\Bundle\SQSBundle\Interfaces;

use LLS\Bundle\SQSBundle\Interfaces\MessageInterface;

/**
 * Define SQS Message Factory valid structure
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
interface MessageFactoryInterface
{
    /**
     * Create a Queue
     *
     * @param string $body Message body
     *
     * @return MessageInterface
     */
    public function create($body = null);
}