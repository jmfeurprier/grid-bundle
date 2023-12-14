<?php

namespace Jmf\Grid\Configuration;

readonly class GridConfiguration
{
    /**
     * @param string[]                $arguments
     * @param ColumnConfiguration[]   $columnConfigurations
     * @param FooterConfiguration[][] $footerConfigurations
     */
    public function __construct(
        private iterable $arguments,
        private KeyValueCollection $gridVariables,
        private iterable $columnConfigurations,
        private RowConfiguration $rowConfiguration,
        private iterable $footerConfigurations,
    ) {
    }

    /**
     * @return string[]
     */
    public function getArguments(): iterable
    {
        return $this->arguments;
    }

    public function getGridVariables(): KeyValueCollection
    {
        return $this->gridVariables;
    }

    /**
     * @return ColumnConfiguration[]
     */
    public function getColumnConfigurations(): iterable
    {
        return $this->columnConfigurations;
    }

    public function getRowConfiguration(): RowConfiguration
    {
        return $this->rowConfiguration;
    }

    /**
     * @return FooterConfiguration[][]
     */
    public function getFooterConfigurations(): iterable
    {
        return $this->footerConfigurations;
    }
}
