<?php
namespace LLS\Bundle\SQSBundle\Tests\Units\Factory;

use mageekguy\atoum\test;
use LLS\Bundle\SQSBundle\Factory;

class QueueFactory extends test
{
    public function testClass()
    {
        $this
            ->assert
                ->class('LLS\Bundle\SQSBundle\Factory\QueueFactory')
                    ->hasInterface('LLS\Bundle\SQSBundle\Interfaces\QueueFactoryInterface')
        ;
    }

    public function testCreateQueue()
    {
        $this
            ->assert
                ->object($queueFactory = new Factory\QueueFactory($this->getMessageFactoryInterfaceMock()))
                    ->isInstanceOf('LLS\Bundle\SQSBundle\Interfaces\QueueFactoryInterface')
                ->object($queue = $queueFactory->create($this->getSQSInterfaceMock(), 'testQueue'))
                    ->isInstanceOf('LLS\Bundle\SQSBundle\Interfaces\QueueInterface')
                    ->string($queue->getName())
                        ->isIdenticalTo('testQueue')
        ;
        
    }

    protected function getSQSInterfaceMock()
    {
        $this->mockGenerator->orphanize('__construct');

        return new \mock\LLS\Bundle\SQSBundle\Model\SQS();
    }

    protected function getMessageFactoryInterfaceMock()
    {
        return new \mock\LLS\Bundle\SQSBundle\Interfaces\MessageFactoryInterface();
    }
}