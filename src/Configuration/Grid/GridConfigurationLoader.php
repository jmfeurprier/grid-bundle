<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Grid;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Column\ColumnConfigurationLoader;
use Jmf\Grid\Configuration\Footer\FooterConfiguration;
use Jmf\Grid\Configuration\Footer\FooterConfigurationLoader;
use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;
use Jmf\Grid\Configuration\Row\RowConfigurationLoader;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Webmozart\Assert\Assert;

readonly class GridConfigurationLoader
{
    public function __construct(
        private ColumnConfigurationLoader $columnConfigurationLoader,
        private RowConfigurationLoader $rowConfigurationLoader,
        private FooterConfigurationLoader $footerConfigurationLoader,
    ) {
    }

    /**
     * @param non-empty-string     $gridId
     * @param array<string, mixed> $config
     *
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    public function load(
        string $gridId,
        array $config,
    ): GridConfiguration {
        Assert::isMap($config);

        return new GridConfiguration(
            $gridId,
            $this->buildGridArguments($config),
            $this->buildGridVariables($config),
            $this->buildColumnConfigurations($config),
            $this->buildRowConfiguration($config),
            $this->buildFooterConfigurations($config),
        );
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return string[]
     */
    private function buildGridArguments(array $config): iterable
    {
        $gridConfig = $config['grid'] ?? [];

        Assert::isMap($gridConfig);

        $argumentsConfig = $gridConfig['arguments'] ?? [];

        if ([] === $argumentsConfig) {
            return [];
        }

        Assert::allString($argumentsConfig);

        return $argumentsConfig;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function buildGridVariables(array $config): KeyValueCollection
    {
        $gridConfig = $config['grid'] ?? [];

        Assert::isMap($gridConfig);

        $variablesConfig = $gridConfig['variables'] ?? [];

        if ([] === $variablesConfig) {
            return KeyValueCollection::createEmpty();
        }

        Assert::isMap($variablesConfig);

        return new KeyValueCollection($variablesConfig);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return ColumnConfiguration[]
     *
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    private function buildColumnConfigurations(array $config): iterable
    {
        $columnsConfig = $config['columns'] ?? [];

        if ([] === $columnsConfig) {
            throw new GridWithoutColumnException();
        }

        Assert::isIterable($columnsConfig);

        $columnConfigurations = [];

        foreach ($columnsConfig as $columnConfig) {
            Assert::isMap($columnConfig);

            $columnConfigurations[] = $this->columnConfigurationLoader->load($columnConfig);
        }

        return $columnConfigurations;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function buildRowConfiguration(array $config): RowConfiguration
    {
        $rowsConfig = $config['rows'] ?? [];

        Assert::isMap($rowsConfig);

        return $this->rowConfigurationLoader->load($rowsConfig);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return FooterConfiguration[][]
     *
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    private function buildFooterConfigurations(array $config): iterable
    {
        $footerRowConfigs = $config['footer'] ?? [];

        if ([] === $footerRowConfigs) {
            return [];
        }

        Assert::isIterable($footerRowConfigs);

        $footerRowConfigurations = [];

        foreach ($footerRowConfigs as $footerColumnConfigs) {
            Assert::isIterable($footerColumnConfigs);

            $footerColumnConfigurations = [];

            foreach ($footerColumnConfigs as $footerColumnConfig) {
                Assert::isMap($footerColumnConfig);

                $footerColumnConfigurations[] = $this->footerConfigurationLoader->load($footerColumnConfig);
            }

            $footerRowConfigurations[] = $footerColumnConfigurations;
        }

        return $footerRowConfigurations;
    }
}
