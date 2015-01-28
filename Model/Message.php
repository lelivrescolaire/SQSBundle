<?php

namespace LLS\Bundle\SQSBundle\Model;

use LLS\Bundle\SQSBundle\Interfaces\MessageInterface;

/**
 * Message Model
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $receiptHandle;

    /**
     * @var string
     */
    protected $body;

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function setReceiptHandle($receiptHandle)
    {
        $this->receiptHandle = $receiptHandle;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getReceiptHandle()
    {
        return $this->receiptHandle;
    }

    /**
     * {@inheritDoc}
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getBody()
    {
        return $this->body;
    }
}