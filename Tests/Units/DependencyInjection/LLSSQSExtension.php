<?php

namespace LLS\Bundle\SQSBundle\Tests\Units\DependencyInjection;

use \mageekguy\atoum\test;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use LLS\Bundle\SQSBundle\DependencyInjection\LLSSQSExtension as Extension;
use LLS\Bundle\SQSBundle\Tests\Utils\ContainerBuilderTest;

/**
 * Test class for LLSSQSExtension
 *
 * @author Jérémy Jourdin <jeremy.jourdin@lelivrescolaire.fr>
 */
class LLSSQSExtension extends ContainerBuilderTest
{
    /**
     * @var Extension
     */
    protected $extension;

    /**
     * Root name of the configuration
     *
     * @var string
     */
    protected $root;

    public function beforeTestMethod($method)
    {
        parent::beforeTestMethod($method);

        $this->extension = new Extension();
        $this->root      = "llssqs";
    }

    public function testGetConfigWithDefaultValues()
    {
        $this->extension->load(array(), $this->container);

        $this
            ->assert

                // Parameters

                ->string($this->container->getParameter($this->root.'.service.type.sqs.class'))
                    ->isEqualTo('LLS\\Bundle\\SQSBundle\\Model\\SQS')

                ->string($this->container->getParameter($this->root.'.model.queue.class'))
                    ->isEqualTo('LLS\\Bundle\\SQSBundle\\Model\\Queue')

                // Services

                ->boolean($this->container->hasDefinition($this->root.'.model.message.factory'))
                    ->isTrue()
                    ->object($definition = $this->container->getDefinition($this->root.'.model.message.factory'))
                        ->string($definition->getClass())
                            ->isEqualTo('%'.$this->root.'.model.message.factory.class%')

                ->boolean($this->container->hasDefinition($this->root.'.model.queue.factory'))
                    ->isTrue()
                    ->object($definition = $this->container->getDefinition($this->root.'.model.queue.factory'))
                        ->string($definition->getClass())
                            ->isEqualTo('%'.$this->root.'.model.queue.factory.class%')

                        // Arguments

                        ->array($arguments = $definition->getArguments())
                            ->hasSize(1)
                        ->object($arguments[0])
                            ->isEqualTo(new Reference('llssqs.model.message.factory'))

                ->boolean($this->container->hasDefinition($this->root.'.service.type.sqs'))
                    ->isTrue()
                    ->object($definition = $this->container->getDefinition($this->root.'.service.type.sqs'))
                        ->string($definition->getClass())
                            ->isEqualTo('%llsaws.service.type.generic.factory.class%')

                        // Tags

                        ->array($tags = $definition->getTags())
                            ->hasSize(1)
                            ->hasKey("llsaws.service.type")
                            ->array($tag = $tags["llsaws.service.type"])
                                ->hasSize(1)
                                    ->array($tag[0])
                                        ->hasSize(1)
                                        ->hasKey("alias")
                                        ->isIdenticalTo(array(
                                            "alias" => "sqs"
                                        ))

                        // Arguments

                        ->array($arguments = $definition->getArguments())
                            ->hasSize(2)
                        ->string($arguments[0])
                            ->isEqualTo('%llssqs.service.type.sqs.class%')
                        ->array($arguments[1])
                            ->hasSize(2)
                                ->string($arguments[1][0])
                                    ->isEqualTo('setQueueFactory')
                                ->object($arguments[1][1])
                                    ->isEqualTo(new Reference('llssqs.model.queue.factory'));
    }

    public function testConfigCreateServices()
    {
        $configs = array(
            array(
                "queues" => array(
                    "bar" => array(
                        "service" => "bar_service",
                        "name"    => "BarQueue"
                    )
                )
            ),
            array(
                "queues" => array(
                    "foo" => array(
                        "service" => "foo_service",
                        "name"    => "FooQueue"
                    )
                )
            ),
        );

        $this->extension->load($configs, $this->container);

        $this
            ->assert
                ->boolean($this->container->hasDefinition($this->root.'.queues.bar'))
                    ->isTrue()
                    ->if($definition = $this->container->getDefinition($this->root.'.queues.bar'))
                    ->then
                        ->string($definition->getClass())
                            ->isEqualTo('LLS\\Bundle\\SQSBundle\\Model\\Queue')
                        ->if($arguments = $definition->getArguments())
                        ->then
                            ->array($arguments)
                                ->hasSize(3)
                            ->object($arguments[0])
                                ->isEqualTo(new Reference('llsaws.services.bar_service'))
                            ->object($arguments[1])
                                ->isEqualTo(new Reference('llssqs.model.message.factory'))
                            ->string($arguments[2])
                                ->isEqualTo('BarQueue')

                ->boolean($this->container->hasDefinition($this->root.'.queues.foo'))
                    ->isTrue()
                    ->if($definition = $this->container->getDefinition($this->root.'.queues.foo'))
                    ->then
                        ->string($definition->getClass())
                            ->isEqualTo('LLS\\Bundle\\SQSBundle\\Model\\Queue')
                        ->if($arguments = $definition->getArguments())
                        ->then
                            ->array($arguments)
                                ->hasSize(3)
                            ->object($arguments[0])
                                ->isEqualTo(new Reference('llsaws.services.foo_service'))
                            ->object($arguments[1])
                                ->isEqualTo(new Reference('llssqs.model.message.factory'))
                            ->string($arguments[2])
                                ->isEqualTo('FooQueue');
    }

    public function testConfigOverridesServices()
    {
        $configs = array(
            array(
                "queues" => array(
                    "bar" => array(
                        "service" => "bar_service",
                        "name"    => "BarQueue"
                    )
                )
            ),
            array(
                "queues" => array(
                    "bar" => array(
                        "name"    => "FooQueue"
                    )
                )
            ),
        );

        $this->extension->load($configs, $this->container);

        $this
            ->assert
                ->boolean($this->container->hasDefinition($this->root.'.queues.bar'))
                    ->isTrue()
                    ->if($definition = $this->container->getDefinition($this->root.'.queues.bar'))
                    ->then
                        ->string($definition->getClass())
                            ->isEqualTo('LLS\\Bundle\\SQSBundle\\Model\\Queue')
                        ->if($arguments = $definition->getArguments())
                        ->then
                            ->array($arguments)
                                ->hasSize(3)
                            ->object($arguments[0])
                                ->isEqualTo(new Reference('llsaws.services.bar_service'))
                            ->object($arguments[1])
                                ->isEqualTo(new Reference('llssqs.model.message.factory'))
                            ->string($arguments[2])
                                ->isEqualTo('FooQueue');
    }
}
