<?php
namespace LLS\Bundle\SQSBundle\Interfaces;

/**
 * Define SQS Message valid structure
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
interface MessageInterface
{
    /**
     * Set Message identifier
     *
     * @param string $id Message Identifier
     *
     * @return {$this}
     */
    public function setId($id);

    /**
     * Get message identifier
     *
     * @return string
     */
    public function getId();

    /**
     * Set Message Receipt handle
     *
     * @param string $receiptHandle Message Receipt handle
     *
     * @return {$this}
     */
    public function setReceiptHandle($receiptHandle);

    /**
     * Get message receipt handle
     *
     * @return string
     */
    public function getReceiptHandle();

    /**
     * Set Message Body
     *
     * @param string $body Message Body
     *
     * @return {$this}
     */
    public function setBody($body);

    /**
     * Get message body
     *
     * @return string
     */
    public function getBody();
}