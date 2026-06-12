<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid;

use Jmf\Grid\Grid\Column\ColumnDefinition;
use Jmf\Grid\Grid\Column\ColumnDefinitionLoader;
use Jmf\Grid\Grid\Footer\FooterDefinition;
use Jmf\Grid\Grid\Footer\FooterDefinitionLoader;
use Jmf\Grid\Grid\KeyValueCollection;
use Jmf\Grid\Grid\Row\RowDefinition;
use Jmf\Grid\Grid\Row\RowDefinitionLoader;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Webmozart\Assert\Assert;

readonly class GridDefinitionLoader
{
    public function __construct(
        private ColumnDefinitionLoader $columnDefinitionLoader,
        private RowDefinitionLoader $rowDefinitionLoader,
        private FooterDefinitionLoader $footerDefinitionLoader,
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
    ): GridDefinition {
        Assert::isMap($config);

        return new GridDefinition(
            $gridId,
            $this->buildGridArguments($config),
            $this->buildGridVariables($config),
            $this->buildColumnDefinitions($config),
            $this->buildRowDefinition($config),
            $this->buildFooterDefinitions($config),
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
     * @return ColumnDefinition[]
     *
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    private function buildColumnDefinitions(array $config): iterable
    {
        $columnsConfig = $config['columns'] ?? [];

        if ([] === $columnsConfig) {
            throw new GridWithoutColumnException();
        }

        Assert::isIterable($columnsConfig);

        $columnDefinitions = [];

        foreach ($columnsConfig as $columnConfig) {
            Assert::isMap($columnConfig);

            $columnDefinitions[] = $this->columnDefinitionLoader->load($columnConfig);
        }

        return $columnDefinitions;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function buildRowDefinition(array $config): RowDefinition
    {
        $rowsConfig = $config['rows'] ?? [];

        Assert::isMap($rowsConfig);

        return $this->rowDefinitionLoader->load($rowsConfig);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return FooterDefinition[][]
     *
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    private function buildFooterDefinitions(array $config): iterable
    {
        $footerRowConfigs = $config['footer'] ?? [];

        if ([] === $footerRowConfigs) {
            return [];
        }

        Assert::isIterable($footerRowConfigs);

        $footerRowDefinitions = [];

        foreach ($footerRowConfigs as $footerColumnConfigs) {
            Assert::isIterable($footerColumnConfigs);

            $footerColumnDefinitions = [];

            foreach ($footerColumnConfigs as $footerColumnConfig) {
                Assert::isMap($footerColumnConfig);

                $footerColumnDefinitions[] = $this->footerDefinitionLoader->load($footerColumnConfig);
            }

            $footerRowDefinitions[] = $footerColumnDefinitions;
        }

        return $footerRowDefinitions;
    }
}
