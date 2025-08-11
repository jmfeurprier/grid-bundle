<?php

declare(strict_types=1);

namespace Jmf\Grid;

use Jmf\Grid\Configuration\CacheableGridConfigurationLoader;
use Jmf\Grid\Configuration\GridConfigurationLoader;
use Jmf\Grid\Configuration\GridConfigurationLoaderInterface;
use Jmf\Grid\RenderingPreset\CacheableRenderingPresetRepository;
use Jmf\Grid\RenderingPreset\RenderingPresetRepository;
use Jmf\Grid\RenderingPreset\RenderingPresetRepositoryInterface;
use Jmf\Grid\Twig\GridExtension;
use Override;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Contracts\Cache\CacheInterface;

class JmfGridBundle extends AbstractBundle
{
    #[Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->fixXmlConfig('preset')
            ->children()
                ->arrayNode('grids')
                    ->info('Grid definitions.')
                    ->useAttributeAsKey('gridId')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('grid')
                                ->children()
                                    ->arrayNode('arguments')
                                        ->useAttributeAsKey('key')
                                        ->variablePrototype()->end()
                                    ->end()
                                    ->arrayNode('variables')
                                        ->useAttributeAsKey('key')
                                        ->variablePrototype()->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('rows')
                                ->variablePrototype()->end()
                            ->end()
                            ->arrayNode('columns')
                                ->isRequired()
                                ->variablePrototype()->end()
                            ->end()
                            ->arrayNode('footer')
                                ->variablePrototype()->end()
                            ->end()
                        ->end()
                    ->end()
//                    ->defaultValue([])
                ->end()
                ->scalarNode('template_path')
                    ->info('Grid template path.')
                    ->defaultValue('@JmfGrid/grid.html.twig')
                ->end()
                ->scalarNode('twig_functions_prefix')
                    ->info('Twig functions prefix.')
                    ->defaultValue(GridExtension::PREFIX_DEFAULT)
                ->end()
                ->arrayNode('presets')
                    ->variablePrototype()->end()
                    ->info('Rendering presets.')
                    ->defaultValue([])
                ->end()
            ->end()
        ;
    }

    #[Override]
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $container->import('../config/services.yaml');

        if (interface_exists(CacheInterface::class)) {
            $container->services()
                ->set(GridConfigurationLoader::class)
                ->autowire()
                ->arg('$gridsConfig', $config['grids'])
            ;

            $container->services()
                ->set(GridConfigurationLoaderInterface::class)
                ->class(CacheableGridConfigurationLoader::class)
                ->autowire()
                ->arg('$gridConfigurationLoader', new Reference(GridConfigurationLoader::class))
            ;
        } else {
            $container->services()
                ->set(GridConfigurationLoaderInterface::class)
                ->class(GridConfigurationLoader::class)
                ->autowire()
                ->arg('$gridsConfig', $config['grids'])
            ;
        }

        if (interface_exists(CacheInterface::class)) {
            $container->services()
                ->set(RenderingPresetRepository::class)
                ->autowire()
                ->arg('$renderingPresetConfigs', $config['presets'])
            ;

            $container->services()
                ->set(RenderingPresetRepositoryInterface::class)
                ->autowire()
                ->class(CacheableRenderingPresetRepository::class)
                ->arg('$renderingPresetRepository', new Reference(RenderingPresetRepository::class))
            ;
        } else {
            $container->services()
                ->set(RenderingPresetRepositoryInterface::class)
                ->autowire()
                ->class(RenderingPresetRepository::class)
                ->arg('$renderingPresetConfigs', $config['presets'])
            ;
        }

        $container->services()
            ->set(GridExtension::class)
            ->autowire()
            ->arg('$templatePath', $config['template_path'])
            ->arg('$prefix', $config['twig_functions_prefix'])
            ->tag('twig.extension')
        ;
    }
}
