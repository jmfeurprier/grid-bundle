<?php

namespace Jmf\Grid;

class GridDefinition
{
    private array $arguments;

    private array $columns;

    private array $gridVariables;

    private array $rowsVariables;

    private $rowsLink;

    private array $footer;

    public function __construct(array $definition)
    {
        $this->arguments     = $definition['arguments'] ?? [];
        $this->columns       = $definition['columns'] ?? [];
        $this->gridVariables = $definition['grid']['variables'] ?? [];
        $this->rowsVariables = $definition['rows']['variables'] ?? [];
        $this->rowsLink      = $definition['rows']['link'] ?? null; // @todo
        $this->footer        = $definition['footer'] ?? [];
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getGridVariables(): array
    {
        return $this->gridVariables;
    }

    public function getRowsVariables(): array
    {
        return $this->rowsVariables;
    }

    public function getRowsLink()
    {
        return $this->rowsLink;
    }

    public function getFooter(): array
    {
        return $this->footer;
    }
}
