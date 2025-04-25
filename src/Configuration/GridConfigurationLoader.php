<?php

namespace Jmf\Grid\Configuration;

use Jmf\Grid\Exception\GridException;
use Override;
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

    #[Override]
    public function load(string $gridId): GridConfiguration
    {
        if (!isset($this->gridsConfig[$gridId])) {
            throw new GridException("Grid with Id '{$gridId}' is not defined.");
        }

        $config = $this->gridsConfig[$gridId];

        Assert::isArray($config);

        return new GridConfiguration(
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

        Assert::allStringNotEmpty($argumentsConfig);

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
     * @throws GridException
     */
    private function buildColumnConfigurations(array $config): iterable
    {
        $columnsConfig = $config['columns'] ?? [];

        if ([] === $columnsConfig) {
            throw new GridException('Grid had no column defined.');
        }

        Assert::isIterable($columnsConfig);

        $columnConfigurations = [];

        foreach ($columnsConfig as $columnConfig) {
            Assert::isArray($columnConfig);

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

        if ([] === $rowsConfig) {
            return RowConfiguration::createEmpty();
        }

        Assert::isArray($rowsConfig);

        return $this->rowConfigurationLoader->load($rowsConfig);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return FooterConfiguration[][]
     */
    private function buildFooterConfigurations(array $config): iterable
    {
        $footerRowConfigs = $config['footer'];

        if ([] === $footerRowConfigs) {
            return [];
        }

        Assert::isIterable($footerRowConfigs);

        $footerRowConfigurations = [];

        foreach ($footerRowConfigs as $footerColumnConfigs) {
            Assert::isIterable($footerColumnConfigs);

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
