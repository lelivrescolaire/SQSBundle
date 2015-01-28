<?php

namespace LLS\Bundle\SQSBundle\Tests\Units\Model;

use LLS\Bundle\SQSBundle\Interfaces\SQSInterface;
use LLS\Bundle\SQSBundle\Model;
use LLS\Bundle\SQSBundle\Tests\Utils\AwsServiceTest;

/**
 * Test class for SQS class
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
class Queue extends AwsServiceTest
{
    public function testClass()
    {
        $this
            ->assert
                ->class("LLS\\Bundle\\SQSBundle\\Model\\Queue")
                    ->hasInterface("LLS\\Bundle\\SQSBundle\\Interfaces\\QueueInterface")
        ;
    }

    public function testInstanciateWithService()
    {
        $this
            ->assert
                ->object($queue = $this->getInstance())
                    ->IsInstanceOf("LLS\\Bundle\\SQSBundle\\Interfaces\\QueueInterface")
        ;
    }

    public function testSendMessages()
    {
        $queue   = $this->getInstance();
        $message = $this->getMessageInterfaceMock();
        $message->getMockController()->getBody = 'This is a test message';
        
        $this
            ->assert

                ->object($this->fixtureGuzzleCall('GetCreatedQueueUrl'))
                ->object($this->fixtureGuzzleCall('SendMessage'))
                ->object($queue->sendMessage($message))
                    ->isInstanceOf("LLS\\Bundle\\SQSBundle\\Interfaces\\MessageInterface")
                    ->mock($message)
                        ->call('getbody')
                            ->once
                        ->call('setId')
                            ->withArguments('5fea7756-0ea4-451a-a703-a558b933e274')
                                ->once
                    ->mock($queue->getSQS()->getClient())
                        ->call('sendMessage')
                            ->withArguments(array(
                                'QueueUrl'     => 'http://sqs.eu-west-1.amazonaws.com/123456789012/testCreate',
                                'MessageBody'  => 'This is a test message',
                                'DelaySeconds' => 0,
                            ))
                                ->once
        ;
    }
  
    public function testFetchMessages()
    {
        $queue   = $this->getInstance();
        $message = $this->getMessageInterfaceMock();
        $message->getMockController()->getBody = 'This is a test message';
        
        $this
            ->assert

                ->object($this->fixtureGuzzleCall('GetCreatedQueueUrl'))
                ->object($this->fixtureGuzzleCall('ReceiveMessage'))
                ->array($fetchedMessages = $queue->fetchMessages())
                    ->hasSize(1)
                    ->object($fetchedMessages[0])
                        ->isInstanceOf("LLS\\Bundle\\SQSBundle\\Interfaces\\MessageInterface")
                    ->mock($queue->getSQS()->getClient())
                        ->call('receiveMessage')
                            ->withArguments(array(
                                'QueueUrl'            => 'http://sqs.eu-west-1.amazonaws.com/123456789012/testCreate',
                                'MaxNumberOfMessages' => 1,
                            ))
                                ->once
        ;
    }
  
    public function testDeleteMessages()
    {
        $queue   = $this->getInstance();
        $message = $this->getMessageInterfaceMock();
        $message->getMockController()->getReceiptHandle = 'MbZj6wDWli+JvwwJaBV+3dcjk2YW2vA3+STFFljTM8tJJg6HRG6PYSasuWXPJB+CwLj1FjgXUv1uSj1gUPAWV66FU/WeR4mq2OKpEGYWbnLmpRCJVAyeMjeU5ZBdtcQ+QEauMZc8ZRv37sIW2iJKq3M9MFx1YvV11A2x/KSbkJ0=';

        $this
            ->assert

                ->object($this->fixtureGuzzleCall('GetCreatedQueueUrl'))
                ->object($this->fixtureGuzzleCall('DeleteMessage'))
                ->object($queue->deleteMessage($message))
                    ->isIdenticalTo($queue)
        ;
    }

    public function testSetAndGetAttributes()
    {
        $queue = $this->getInstance();

        $this
            ->assert
        
                // SetQueueAttribute

                ->object($this->fixtureGuzzleCall('GetCreatedQueueUrl'))
                ->object($this->fixtureGuzzleCall('SetAttribute'))
                ->object($queue->setAttributes(array()))
                    ->isIdenticalTo($queue)

                // GetQueueAttribute
                
                ->object($this->fixtureGuzzleCall('GetAttribute'))
                ->array($attributes = $queue->getAttributes())
                    ->hasSize(9)
                    ->string($attributes['ReceiveMessageWaitTimeSeconds'])
                        ->isEqualTo('2')
                    ->string($attributes['VisibilityTimeout'])
                        ->isEqualTo('30')
                    ->string($attributes['ApproximateNumberOfMessages'])
                        ->isEqualTo('0')
                    ->string($attributes['ApproximateNumberOfMessagesNotVisible'])
                        ->isEqualTo('0')
                    ->string($attributes['CreatedTimestamp'])
                        ->isEqualTo('1286771522')
                    ->string($attributes['LastModifiedTimestamp'])
                        ->isEqualTo('1286771522')
                    ->string($attributes['QueueArn'])
                        ->isEqualTo('arn:aws:sqs:eu-west-1:123456789012:testCreate')
                    ->string($attributes['MaximumMessageSize'])
                        ->isEqualTo('8192')
                    ->string($attributes['MessageRetentionPeriod'])
                        ->isEqualTo('345600')
        ;
    }

    public function testAddAndRemovePermissions()
    {
        $queue = $this->getInstance();

        $this
            ->assert
        
                // AddPermission

                ->object($this->fixtureGuzzleCall('GetCreatedQueueUrl'))
                ->object($this->fixtureGuzzleCall('AddPermission'))
                ->object($queue->addPermission('test', array(), array()))
                    ->isIdenticalTo($queue)

                // RemovePermission
                
                ->object($this->fixtureGuzzleCall('RemovePermission'))
                ->object($queue->removePermission('test'))
                    ->isIdenticalTo($queue)
        ;
    }

    protected function getInstance()
    {
        $sqs = new \mock\LLS\Bundle\SQSBundle\Model\SQS(
            $this->getIdentityInterfaceMock(),
            $this->getClientFactoryInterfaceMock()
        );

        if ($this->guzzleMock) {
            $sqs->getClient()->addSubscriber($this->guzzleMock);
        }

        return new Model\Queue($sqs, $this->getMessageFactoryInterfaceMock(), 'test');
    }

    protected function getMessageInterfaceMock()
    {
        return new \mock\LLS\Bundle\SQSBundle\Interfaces\MessageInterface();
    }

    protected function getQueueFactoryInterfaceMock()
    {
        return new \mock\LLS\Bundle\SQSBundle\Interfaces\QueueFactoryInterface();
    }

    protected function getMessageFactoryInterfaceMock()
    {
        $mock    = new \mock\LLS\Bundle\SQSBundle\Interfaces\MessageFactoryInterface();
        $service = $this;

        $mock->getMockController()->create = function ($body) use ($service) {
            $message = $service->getMessageInterfaceMock();
            $message->getMockController()->getBody          = $body;
            $message->getMockController()->setId            = $message;
            $message->getMockController()->setReceiptHandle = $message;

            return $message;
        };

        return $mock;
    }
}