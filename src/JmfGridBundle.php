<?php

declare(strict_types=1);

namespace Jmf\Grid;

use Jmf\Grid\Configuration\GridConfigurationFileLoader;
use Jmf\Grid\Exception\DuplicateGridException;
use Override;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class JmfGridBundle extends AbstractBundle
{
    /**
     * @const array<non-empty-string, non-empty-string>
     */
    private const array PARAMETERS_MAPPING = [
        'grids'                 => 'grid_configurations',
        'template_path'         => 'template_path',
        'twig_functions_prefix' => 'twig_functions_prefix',
    ];

    protected string $extensionAlias = 'jmf_grid';

    public function __construct(
        private readonly GridConfigurationFileLoader $gridConfigurationFileLoader = new GridConfigurationFileLoader(),
    ) {
    }

    #[Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->import('../config/definition.php');
    }

    /**
     * @param array<string, mixed> $config
     *
     * @throws DuplicateGridException
     */
    #[Override]
    public function loadExtension(
        array $config,
        ContainerConfigurator $container,
        ContainerBuilder $builder,
    ): void {
        $container->import('../config/services.yaml');

        // The `grids` config (inline + per-grid files) is assembled by the loader.
        $config['grids'] = $this->gridConfigurationFileLoader->load($config, $builder, $this->extensionAlias);

        $this->loadParameters($config, $container);
    }

    /**
     * @param array<string, mixed> $config
     */
    private function loadParameters(
        array $config,
        ContainerConfigurator $container,
    ): void {
        foreach (self::PARAMETERS_MAPPING as $configKey => $property) {
            $container->parameters()->set(
                "{$this->extensionAlias}.{$property}",
                $config[$configKey],
            );
        }
    }
}
