<?php

namespace Jmf\CrudEngine\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('jmf_grid');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('grids')
                    ->info('Grid definitions.')
                    ->useAttributeAsKey('class')
                    ->variablePrototype()
                ->end()
                ->scalarNode('template_path')
                    ->info('Grid template path.')
                    ->defaultValue('@JmfGrid/grid.html.twig')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
