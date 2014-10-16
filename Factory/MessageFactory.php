<?php
namespace LLS\Bundle\SQSBundle\Factory;

use LLS\Bundle\SQSBundle\Interfaces\MessageFactoryInterface;
use LLS\Bundle\SQSBundle\Model\Message;

/**
 * Factory class for Message model
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
class MessageFactory implements MessageFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function create($body = null)
    {
        $message = new Message();

        return $message->setBody($body);
    }
}