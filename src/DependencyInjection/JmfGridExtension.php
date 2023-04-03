<?php

namespace Jmf\CrudEngine\DependencyInjection;

use App\Grid\GridGenerator;
use App\Twig\GridExtension;
use Jmf\CrudEngine\Loading\RouteLoader;
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

        $containerBuilder->autowire(GridGenerator::class)
            ->setArgument('$', $config['template_path'])
        ;

        $containerBuilder->autowire(GridExtension::class)
            ->setArgument('$templatePath', $config['template_path'])
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
