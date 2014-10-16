<?php

namespace LLS\Bundle\SQSBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

use LLS\Bundle\AWSBundle\DependencyInjection\LLSAWSExtension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LLSSQSExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!empty($config['queues'])) {
            $this->loadQueues($container, $config['queues']);
        }
    }

    /**
     * Load queues from user configuration
     *
     * @param ContainerBuilder $container SF2 Container Builder
     * @param array            $config    Configuration array
     *
     * @return {$this}
     */
    public function loadQueues(ContainerBuilder $container, array $config)
    {
        foreach ($config as $name => $attributes) {
            $container
                ->setDefinition(
                    self::getQueueServiceKey($name),
                    new Definition(
                        $container->getParameter('llssqs.model.queue.class'),
                        array(
                            new Reference(
                                LLSAWSExtension::getServiceServiceKey($attributes['service'])
                            ),
                            $attributes['name']
                        )
                    )
                );
        }

        return $this;
    }

    /**
     * Get Queue Service key from it's name
     *
     * @param string $name Service name
     *
     * @return string
     */
    public static function getQueueServiceKey($name)
    {
        return sprintf('llssqs.queues.%s', $name);
    }
}
