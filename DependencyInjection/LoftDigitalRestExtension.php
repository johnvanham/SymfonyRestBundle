<?php

namespace LoftDigital\SymfonyRestBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class RssCustomerApiExtension
 *
 * This is the class that loads and manages your bundle configuration
 *
 * @package Rss\CustomerApiBundle\DependencyInjection
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class LoftDigitalRestExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        $container->setParameter('loft_digital_rest.range_listener.max', $processedConfig['range_listener']['max']);
        $container->setParameter('loft_digital_rest.range_listener.order', $processedConfig['range_listener']['order']);
        $container->setParameter(
            'loft_digital_rest.range_listener.max_limit',
            $processedConfig['range_listener']['max_limit']
        );
        $container->setParameter(
            'loft_digital_rest.range_listener.offset',
            $processedConfig['range_listener']['offset']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'loft_digital_rest';
    }
}
