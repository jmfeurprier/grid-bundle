<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Grid;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Footer\FooterConfiguration;
use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;

readonly class GridConfiguration
{
    /**
     * @param non-empty-string        $id
     * @param string[]                $arguments
     * @param ColumnConfiguration[]   $columnConfigurations
     * @param FooterConfiguration[][] $footerConfigurations
     */
    public function __construct(
        private string $id,
        private iterable $arguments,
        private KeyValueCollection $gridVariables,
        private iterable $columnConfigurations,
        private RowConfiguration $rowConfiguration,
        private iterable $footerConfigurations,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getId(): string
    {
        return $this->id;
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
