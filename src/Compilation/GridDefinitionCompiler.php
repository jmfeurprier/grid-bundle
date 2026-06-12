<?php

declare(strict_types=1);

namespace Jmf\Grid\Compilation;

use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\Grid\Definition\FooterDefinition;
use Jmf\Grid\Definition\GridDefinition;
use Jmf\Grid\Definition\RowDefinition;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\Grid\Definition\KeyValueCollection;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Webmozart\Assert\Assert;

readonly class GridDefinitionCompiler
{
    public function __construct(
        private ColumnDefinitionCompiler $columnDefinitionCompiler,
        private RowDefinitionCompiler $rowDefinitionCompiler,
        private FooterDefinitionCompiler $footerDefinitionCompiler,
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
    public function compile(
        string $gridId,
        array $config,
    ): GridDefinition {
        Assert::isMap($config);

        return new GridDefinition(
            $gridId,
            $this->buildGridArguments($config),
            $this->buildGridVariables($config),
            $this->buildColumnDefinitions($gridId, $config),
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
     * @param non-empty-string     $gridId
     * @param array<string, mixed> $config
     *
     * @return ColumnDefinition[]
     *
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    private function buildColumnDefinitions(string $gridId, array $config): iterable
    {
        $columnsConfig = $config['columns'] ?? [];

        if ([] === $columnsConfig) {
            throw new GridWithoutColumnException($gridId);
        }

        Assert::isIterable($columnsConfig);

        $columnDefinitions = [];

        foreach ($columnsConfig as $columnConfig) {
            Assert::isMap($columnConfig);

            $columnDefinitions[] = $this->columnDefinitionCompiler->compile(
                $columnConfig,
            );
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

        return $this->rowDefinitionCompiler->compile($rowsConfig);
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

                $footerColumnDefinitions[] = $this->footerDefinitionCompiler->compile(
                    $footerColumnConfig,
                );
            }

            $footerRowDefinitions[] = $footerColumnDefinitions;
        }

        return $footerRowDefinitions;
    }
}
