<?php
namespace LLS\Bundle\SQSBundle\Interfaces;

/**
 * Define SQS Queue valid structure
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
interface QueueInterface
{
    /**
     * Constructor
     *
     * @param SQSInterface            $sqs            SQS Service
     * @param MessageFactoryInterface $messageFactory Message Factory
     * @param string                  $name           Queue name
     */
    public function __construct(SQSInterface $sqs, MessageFactoryInterface $messageFactory, $name);

    /**
     * Get Message Factory
     *
     * @return MessageFactoryInterface
     */
    public function getMessageFactory();

    /**
     * Get Queue name
     *
     * @return string Queue name
     */
    public function getName();

    /**
     * Get Queue ARN
     *
     * @return string Queue ARN
     */
    public function getARN();

    /**
     * Get Queue size
     *
     * @return integer Queue size
     */
    public function getSize();

    /**
     * Get Queue URL
     *
     * @return string Queue URL
     */
    public function getUrl();

    /**
     * Add a Queue permission
     *
     * @param string $label        Permission label
     * @param array  $awsAccountId Accounts identifiers
     * @param array  $actionName   Actions names
     *
     * @return boolean
     */
    public function addPermission($label, array $awsAccountId, array $actionName);

    /**
     * Remove a Queue permission
     *
     * @param string $label Permission label
     *
     * @return boolean
     */
    public function removePermission($label);

    /**
     * Remove a Queue message
     *
     * @param MessageInterface $message Message to delete
     *
     * @return boolean
     */
    public function deleteMessage(MessageInterface $message);

    /**
     * Add attributes to a Queue
     *
     * @param array $attributes Attributes to add
     *
     * @return boolean
     */
    public function setAttributes(array $attributes);

    /**
     * Get Queue attributes
     *
     * @param array $attributes Attributes names
     *
     * @return array
     */
    public function getAttributes(array $attributes);

    /**
     * Get Queue Messages
     *
     * @param interger   $nb  Number of messages to fetch
     * @param array|null $opt Fetch options
     *
     * @return array<Message>
     */
    public function fetchMessages($nb=1, array $opt = null);

    /**
     * Send a message to the queue
     *
     * @param Message $message Message to send
     * @param integer $delay   Message visibility delay
     *
     * @return boolean
     */
    public function sendMessage(MessageInterface $message, $delay = 0);
}