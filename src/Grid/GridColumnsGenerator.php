<?php

namespace Jmf\Grid;

class GridColumnsGenerator
{
    public function generate(GridDefinition $gridDefinition): array
    {
        $columns = [];

        foreach ($gridDefinition->getColumns() as $columnDefinition) {
            $columns[] = $this->generateColumn($columnDefinition);
        }

        return $columns;
    }

    private function generateColumn(array $columnDefinition): array
    {
        $column = [];

        if (array_key_exists('align', $columnDefinition)) {
            $column['align'] = $columnDefinition['align'];
        }

        if (array_key_exists('label', $columnDefinition)) {
            $column['label'] = $columnDefinition['label'];
        }

        return $column;
    }
}
