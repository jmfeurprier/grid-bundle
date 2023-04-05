<?php

namespace Jmf\Grid\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('jmf_grid');

        $treeBuilder->getRootNode()
            ->fixXmlConfig('macro')
            ->fixXmlConfig('preset')
            ->children()
                ->arrayNode('grids')
                    ->info('Grid definitions.')
                    ->useAttributeAsKey('key')
                    ->variablePrototype()->end()
                    ->defaultValue([])
                ->end()
                ->scalarNode('template_path')
                    ->info('Grid template path.')
                    ->defaultValue('@JmfGrid/grid.html.twig')
                ->end()
                ->arrayNode('macros')
                    ->scalarPrototype()->end()
                    ->info('Grid macros to import.')
                    ->defaultValue([])
                ->end()
                ->arrayNode('presets')
                    ->variablePrototype()->end()
                    ->info('Rendering presets.')
                    ->defaultValue([])
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
