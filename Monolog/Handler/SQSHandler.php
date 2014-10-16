<?php
namespace LLS\Bundle\SQSBundle\Monolog\Handler;

use AmazonSQS;

use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class SQSHandler extends AbstractProcessingHandler
{
    /**
     * @var AmazonSQS SQS client
     */
    protected $sqs;

    /**
     * @var string Queue Name
     */
    protected $queueUrl;

    public function __construct(AmazonSQS $sqs, $queueUrl, $level = Logger::INFO, $bubble = true)
    {
        $this->sqs      = $sqs;
        $this->queueUrl = $queueUrl;

        $level = is_int($level) ? $level : constant('Monolog\Logger::'.strtoupper($level));

        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritDoc}
     */
    protected function write(array $record)
    {
        $message = $record["formatted"];

        $this->sqs->send_message($this->queueUrl, $message);
    }
    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new JsonFormatter(JsonFormatter::BATCH_MODE_JSON, false);
    }
}