<?php

namespace Jmf\Grid\DependencyInjection;

use Jmf\Grid\Grid\GridDefinitionLoader;
use Jmf\Grid\Grid\GridFooterGenerator;
use Jmf\Grid\Grid\GridRowCellGenerator;
use Jmf\Grid\Grid\GridRowGenerator;
use Jmf\Grid\Grid\GridRowsGenerator;
use Jmf\Grid\Twig\GridExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class JmfGridExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(
        array $configs,
        ContainerBuilder $containerBuilder
    ) {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        $containerBuilder->autowire(GridDefinitionLoader::class)
            ->setArgument('$gridDefinitions', $config['grids'])
            ->setArgument('$entityRenderingPresets', $config['presets'])
        ;

        $containerBuilder->autowire(GridExtension::class)
            ->setArgument('$templatePath', $config['template_path'])
            ->setArgument('$prefix', $config['twig_functions_prefix'])
            ->addTag('twig.extension')
        ;

        $containerBuilder->autowire(GridFooterGenerator::class)
            ->setArgument('$entityRenderingPresets', $config['presets'])
        ;

        $containerBuilder->autowire(GridRowGenerator::class)
            ->setArgument('$macros', $config['macros'])
        ;

        $containerBuilder->autowire(GridRowCellGenerator::class)
            ->setArgument('$macros', $config['macros'])
        ;
    }

    public function getNamespace(): string
    {
        return '';
    }

    public function getXsdValidationBasePath(): bool
    {
        return false;
    }

    public function getAlias(): string
    {
        return 'jmf_grid';
    }
}
