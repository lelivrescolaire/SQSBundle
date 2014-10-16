<?php
namespace LLS\Bundle\SQSBundle\Tests\Units\Model;

use mageekguy\atoum\test;
use LLS\Bundle\SQSBundle\Model;

class Message extends test
{
    public function testClass()
    {
        $this
            ->assert
                ->class('LLS\Bundle\SQSBundle\Model\Message')
                    ->hasInterface('LLS\Bundle\SQSBundle\Interfaces\MessageInterface')
        ;
    }

    public function testGettersAndSetters()
    {
        $this
            ->assert
                ->object($message = new Model\Message())

                // Defaults

                ->variable($message->getId())
                    ->isNull()
                ->variable($message->getReceiptHandle())
                    ->isNull()
                ->variable($message->getBody())
                    ->isNull()

                // I/O

                ->object($message->setId($id = '5fea7756-0ea4-451a-a703-a558b933e274'))
                    ->isIdenticalTo($message)
                    ->string($message->getId())
                        ->isIdenticalTo($id)
                ->object($message->setReceiptHandle($receiptHandle = 'MbZj6wDWli+JvwwJaBV+3dcjk2YW2vA3+STFFljTM8tJJg6HRG6PYSasuWXPJB+CwLj1FjgXUv1uSj1gUPAWV66FU/WeR4mq2OKpEGYWbnLmpRCJVAyeMjeU5ZBdtcQ+QEauMZc8ZRv37sIW2iJKq3M9MFx1YvV11A2x/KSbkJ0='))
                    ->isIdenticalTo($message)
                    ->string($message->getReceiptHandle())
                        ->isIdenticalTo($receiptHandle)
                ->object($message->setBody($body = 'This is a test message'))
                    ->isIdenticalTo($message)
                    ->string($message->getBody())
                        ->isIdenticalTo($body)
        ;
        
    }
}