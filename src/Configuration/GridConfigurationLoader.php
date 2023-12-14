<?php

namespace Jmf\Grid\Configuration;

use DomainException;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

readonly class GridConfigurationLoader implements GridConfigurationLoaderInterface
{
    /**
     * @param array<string, mixed> $gridsConfig
     */
    public function __construct(
        private ColumnConfigurationLoader $columnConfigurationLoader,
        private RowConfigurationLoader $rowConfigurationLoader,
        private FooterConfigurationLoader $footerConfigurationLoader,
        private array $gridsConfig,
    ) {
    }

    /**
     * @throws DomainException
     */
    public function load(string $gridId): GridConfiguration
    {
        if (!isset($this->gridsConfig[$gridId])) {
            throw new DomainException("Grid with Id '{$gridId}' is not defined.");
        }

        $gridConfig = $this->gridsConfig[$gridId];

        Assert::isArray($gridConfig);

        return new GridConfiguration(
            $this->buildGridArguments($gridConfig),
            $this->buildGridVariables($gridConfig),
            $this->buildColumnConfigurations($gridConfig),
            $this->buildRowConfiguration($gridConfig),
            $this->buildFooterConfigurations($gridConfig),
        );
    }

    /**
     * @param array<string, mixed> $gridConfig
     *
     * @return string[]
     */
    private function buildGridArguments(array $gridConfig): iterable
    {
        if (!isset($gridConfig['arguments'])) {
            return [];
        }

        $argumentsConfig = $gridConfig['arguments'];

        Assert::allString($argumentsConfig);

        return $argumentsConfig;
    }

    /**
     * @param array<string, mixed> $gridConfig
     */
    private function buildGridVariables(array $gridConfig): KeyValueCollection
    {
        if (!array_key_exists('grid', $gridConfig)) {
            return KeyValueCollection::createEmpty();
        }

        Assert::isMap($gridConfig['grid']);

        if (!array_key_exists('variables', $gridConfig['grid'])) {
            return KeyValueCollection::createEmpty();
        }

        $variablesConfig = $gridConfig['grid']['variables'];

        Assert::isMap($variablesConfig);

        return new KeyValueCollection($variablesConfig);
    }

    /**
     * @param array<string, mixed> $gridConfig
     *
     * @return ColumnConfiguration[]
     */
    private function buildColumnConfigurations(array $gridConfig): iterable
    {
        if (!isset($gridConfig['columns'])) {
            throw new InvalidArgumentException();
        }

        $columnsConfig = $gridConfig['columns'];

        Assert::isIterable($columnsConfig);

        $columnConfigurations = [];

        foreach ($columnsConfig as $columnConfig) {
            Assert::isArray($columnConfig);

            $columnConfigurations[] = $this->columnConfigurationLoader->load($columnConfig);
        }

        return $columnConfigurations;
    }

    /**
     * @param array<string, mixed> $gridConfig
     */
    private function buildRowConfiguration(array $gridConfig): RowConfiguration
    {
        if (!isset($gridConfig['rows'])) {
            return RowConfiguration::createEmpty();
        }

        $rowsConfig = $gridConfig['rows'];

        Assert::isArray($rowsConfig);

        return $this->rowConfigurationLoader->load($rowsConfig);
    }

    /**
     * @param array<string, mixed> $gridConfig
     *
     * @return FooterConfiguration[][]
     */
    private function buildFooterConfigurations(array $gridConfig): iterable
    {
        if (!isset($gridConfig['footer'])) {
            throw new InvalidArgumentException();
        }

        $footerRowConfigs = $gridConfig['footer'];

        Assert::allIsIterable($footerRowConfigs);

        $footerRowConfigurations = [];

        foreach ($footerRowConfigs as $footerColumnConfigs) {
            $footerColumnConfigurations = [];

            foreach ($footerColumnConfigs as $footerColumnConfig) {
                Assert::isArray($footerColumnConfig);

                $footerColumnConfigurations[] = $this->footerConfigurationLoader->load($footerColumnConfig);
            }

            $footerRowConfigurations[] = $footerColumnConfigurations;
        }

        return $footerRowConfigurations;
    }
}
