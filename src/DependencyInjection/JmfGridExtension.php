<?php

namespace Jmf\Grid\DependencyInjection;

use Jmf\Grid\Configuration\CacheableGridConfigurationLoader;
use Jmf\Grid\Configuration\GridConfigurationLoader;
use Jmf\Grid\Configuration\GridConfigurationLoaderInterface;
use Jmf\Grid\Grid\GridRowCellGenerator;
use Jmf\Grid\Grid\GridRowGenerator;
use Jmf\Grid\RenderingPreset\CacheableRenderingPresetRepository;
use Jmf\Grid\RenderingPreset\RenderingPresetRepository;
use Jmf\Grid\RenderingPreset\RenderingPresetRepositoryInterface;
use Jmf\Grid\Twig\GridExtension;
use Override;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\Cache\CacheInterface;

class JmfGridExtension extends Extension
{
    #[Override]
    public function load(
        array $configs,
        ContainerBuilder $containerBuilder,
    ): void {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        if (interface_exists(CacheInterface::class)) {
            $containerBuilder->autowire(GridConfigurationLoader::class)
                ->setArgument('$gridsConfig', $config['grids'])
            ;

            $containerBuilder->autowire(GridConfigurationLoaderInterface::class)
                ->setClass(CacheableGridConfigurationLoader::class)
                ->setArgument('$gridConfigurationLoader', new Reference(GridConfigurationLoader::class))
            ;
        } else {
            $containerBuilder->autowire(GridConfigurationLoaderInterface::class)
                ->setClass(GridConfigurationLoader::class)
                ->setArgument('$gridsConfig', $config['grids'])
            ;
        }

        if (interface_exists(CacheInterface::class)) {
            $containerBuilder->autowire(RenderingPresetRepository::class)
                ->setArgument('$renderingPresetConfigs', $config['presets'])
            ;

            $containerBuilder->autowire(RenderingPresetRepositoryInterface::class)
                ->setClass(CacheableRenderingPresetRepository::class)
                ->setArgument('$renderingPresetRepository', new Reference(RenderingPresetRepository::class))
            ;
        } else {
            $containerBuilder->autowire(RenderingPresetRepositoryInterface::class)
                ->setClass(RenderingPresetRepository::class)
                ->setArgument('$renderingPresetConfigs', $config['presets'])
            ;
        }

        $containerBuilder->autowire(GridExtension::class)
            ->setArgument('$templatePath', $config['template_path'])
            ->setArgument('$prefix', $config['twig_functions_prefix'])
            ->addTag('twig.extension')
        ;

        $containerBuilder->autowire(GridRowCellGenerator::class)
            ->setArgument('$macros', $config['macros'])
        ;

        $containerBuilder->autowire(GridRowGenerator::class)
            ->setArgument('$macros', $config['macros'])
        ;
    }

    #[Override]
    public function getNamespace(): string
    {
        return '';
    }

    #[Override]
    public function getXsdValidationBasePath(): bool
    {
        return false;
    }

    #[Override]
    public function getAlias(): string
    {
        return 'jmf_grid';
    }
}
