<?php

namespace Jmf\Grid\Grid;

class GridDefinition
{
    /**
     * @var array<string, mixed>
     */
    private array $arguments;

    /**
     * @var iterable<array<string, mixed>>
     */
    private iterable $columnDefinitions;

    /**
     * @var array<string, mixed>
     */
    private array $gridVariables;

    /**
     * @var array<string, mixed>
     */
    private array $rowsVariables;

    private $rowsLink;

    /**
     * @var array<string, mixed>
     */
    private array $footerDefinition;

    public function __construct(array $definition)
    {
        $this->arguments         = $definition['arguments'] ?? [];
        $this->columnDefinitions = $definition['columns'] ?? [];
        $this->gridVariables     = $definition['grid']['variables'] ?? [];
        $this->rowsVariables     = $definition['rows']['variables'] ?? [];
        $this->rowsLink          = $definition['rows']['link'] ?? null; // @todo
        $this->footerDefinition  = $definition['footer'] ?? [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @return iterable<array<string, mixed>>
     */
    public function getColumnDefinitions(): iterable
    {
        return $this->columnDefinitions;
    }

    /**
     * @return array<string, mixed>
     */
    public function getGridVariables(): array
    {
        return $this->gridVariables;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRowsVariables(): array
    {
        return $this->rowsVariables;
    }

    public function getRowsLink()
    {
        return $this->rowsLink;
    }

    public function getFooterDefinition(): array
    {
        return $this->footerDefinition;
    }
}
