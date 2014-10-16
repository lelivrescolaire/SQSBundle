<?php

namespace LLS\Bundle\SQSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('llssqs');
        $rootNode
            ->fixXmlConfig('queue')
            ->append($this->getQueuesConfig());

        return $treeBuilder;
    }

    protected function getQueuesConfig()
    {
        $treeBuilder = new TreeBuilder();

        $node = $treeBuilder->root('queues');
        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('id')
            ->prototype('array')
                ->children()
                    ->scalarNode('service')
                        ->isRequired()
                    ->end()
                    ->scalarNode('name')
                        ->isRequired()
                    ->end()
                ->end()
            ->end();
        ;

        return $node;
    }
}
