<?php

namespace LLS\Bundle\SQSBundle\Tests\Units\Model;

use LLS\Bundle\SQSBundle\Model;
use LLS\Bundle\SQSBundle\Tests\Utils\AwsServiceTest;

/**
 * Test class for SQS class
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
class SQS extends AwsServiceTest
{
    public function testClass()
    {
        $this
            ->assert
                ->class("LLS\\Bundle\\SQSBundle\\Model\\SQS")
                    ->hasInterface("LLS\\Bundle\\AWSBundle\\Interfaces\\ServiceInterface")
                    ->hasInterface("LLS\\Bundle\\SQSBundle\\Interfaces\\SQSInterface")
        ;
    }

    public function testInstanciateWithClient()
    {
        $this
            ->assert
                ->object($sqs = $this->getInstance())
                    ->isInstanceOf("LLS\\Bundle\\AWSBundle\\Interfaces\\ServiceInterface")
                    ->isInstanceOf("LLS\\Bundle\\SQSBundle\\Interfaces\\SQSInterface")
                ->object($sqs->getClient())
                    ->isInstanceOf("Aws\\Sqs\\SqsClient")
        ;
    }

    public function testCreateQueues()
    {
        $sqs = $this->getInstance();
        $queue = $this->getQueueInterfaceMock();
        $queue->getMockController()->getName = 'testCreate';

        $this
            ->assert

                ->object($this->fixtureGuzzleCall('CreateQueue'))
                ->object($sqs->createQueue($queue))
                    ->isInstanceOf("LLS\\Bundle\\SQSBundle\\Interfaces\\QueueInterface")
                    ->isIdenticalTo($queue)
                    ->mock($queue)
                        ->call('getName')
                            ->once()
                        ->call('setUrl')
                            ->withArguments('http://sqs.eu-west-1.amazonaws.com/123456789012/testCreate')
                                ->once()
                    ->mock($sqs->getClient())
                        ->call('createQueue')
                            ->withArguments(array(
                                "QueueName" => "testCreate"
                            ))
                                ->once()
        ;
    }

    public function testDeleteQueues()
    {
        $sqs = $this->getInstance();
        $queue = $this->getQueueInterfaceMock();
        $queue->getMockController()->getUrl = 'http://sqs.eu-west-1.amazonaws.com/123456789012/testCreate';

        $this
            ->assert

                ->object($this->fixtureGuzzleCall('DeleteQueue'))
                ->object($sqs->deleteQueue($queue))
                    ->isInstanceOf("LLS\\Bundle\\SQSBundle\\Interfaces\\SQSInterface")
                    ->isIdenticalTo($sqs)
                    ->mock($queue)
                        ->call('getUrl')
                            ->once()
        ;
    }

    public function testRetreiveQueues()
    {
        $sqs = $this->getInstance();

        $this
            ->assert

                // GetQueue

                ->object($queueGetByName = $sqs->getQueue('testGetQueue'))
                    ->isInstanceOf("LLS\\Bundle\\SQSBundle\\Interfaces\\QueueInterface")
                    ->mock($sqs->getQueueFactory())
                        ->call('create')
                            ->withArguments($sqs, 'testGetQueue')
                                ->once

                // ListQueues

                ->object($this->fixtureGuzzleCall('ListQueues'))
                ->array($queues = $sqs->listQueues())
                    ->hasSize(1)
                    ->object($queueList = $queues[0])
                        ->isInstanceOf("LLS\\Bundle\\SQSBundle\\Interfaces\\QueueInterface")
                    ->mock($sqs->getQueueFactory())
                        ->call('getNameFromUrl')
                            ->withArguments('http://sqs.eu-west-1.amazonaws.com/123456789012/testList')
                                ->once
                        ->call('create')
                            ->withArguments($sqs, 'testList')
                                ->once
        ;
    }

    protected function getInstance()
    {
        $instance = new Model\SQS(
            $this->getIdentityInterfaceMock(),
            $this->getClientFactoryInterfaceMock()
        );

        $instance->setQueueFactory($this->getQueueFactoryInterfaceMock());

        if ($this->guzzleMock) {
            $instance->getClient()->addSubscriber($this->guzzleMock);
        }

        return $instance;
    }

    protected function getQueueFactoryInterfaceMock()
    {
        $this->mockGenerator->orphanize('__construct');

        $mock = new \mock\LLS\Bundle\SQSBundle\Interfaces\QueueFactoryInterface();
        $test = $this;

        $mock->getMockController()->create = function ($sqs, $name) use ($test) {
            return $test->getQueueInterfaceMock();
        };

        $mock->getMockController()->getNameFromUrl = function ($url)
        {
            $expUrl = explode('/', $url);
            $name   = array_pop($expUrl);

            return trim($name);
        };

        return $mock;
    }

    protected function getQueueInterfaceMock()
    {
        $this->mockGenerator->orphanize('__construct');

        return new \mock\LLS\Bundle\SQSBundle\Interfaces\QueueInterface();
    }
}