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

        $this->getMonologConfig($rootNode);

        return $treeBuilder;
    }

    protected function getMonologConfig($rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('monolog')
                    ->children()
                        ->arrayNode('handler')
                            ->children()
                                ->scalarNode('queueUrl')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('level')->end()
                                ->scalarNode('bubble')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $this;
    }
}
