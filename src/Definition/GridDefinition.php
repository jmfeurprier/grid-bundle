<?php

declare(strict_types=1);

namespace Jmf\Grid\Definition;

use Jmf\Grid\Definition\KeyValueCollection;

readonly class GridDefinition
{
    /**
     * @param non-empty-string     $id
     * @param string[]             $arguments
     * @param ColumnDefinition[]   $columnDefinitions
     * @param FooterDefinition[][] $footerDefinitions
     */
    public function __construct(
        private string $id,
        private iterable $arguments,
        private KeyValueCollection $gridVariables,
        private iterable $columnDefinitions,
        private RowDefinition $rowDefinition,
        private iterable $footerDefinitions,
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
     * @return ColumnDefinition[]
     */
    public function getColumnDefinitions(): iterable
    {
        return $this->columnDefinitions;
    }

    public function getRowDefinition(): RowDefinition
    {
        return $this->rowDefinition;
    }

    /**
     * @return FooterDefinition[][]
     */
    public function getFooterDefinitions(): iterable
    {
        return $this->footerDefinitions;
    }
}
