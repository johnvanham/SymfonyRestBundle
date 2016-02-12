<?php

namespace LoftDigital\SymfonyRestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * This is the class that validates and merges configuration from your app/config files
 *
 * @package LoftDigital\SymfonyRestBundle\DependencyInjection
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('loft_digital_symfony_rest');
        $rootNode
            ->children()
                ->arrayNode('range_listener')
                    ->children()
                        ->integerNode('max_limit')->min(0)->defaultValue(1000)->end()
                        ->integerNode('max')->min(0)->defaultValue(200)->end()
                        ->integerNode('offset')->min(0)->defaultValue(0)->end()
                        ->scalarNode('order')
                            ->defaultValue('asc')
                            ->validate()
                            ->ifNotInArray(array('asc', 'desc'))
                            ->thenInvalid('Invalid order "%s"')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
