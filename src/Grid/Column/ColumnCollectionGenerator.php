<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Column;

use Jmf\Grid\Grid\Column\ColumnDefinition;
use Jmf\Grid\Grid\GridDefinition;

readonly class ColumnCollectionGenerator
{
    public function generate(GridDefinition $gridDefinition): ColumnCollection
    {
        $columns = [];

        foreach ($gridDefinition->getColumnDefinitions() as $columnDefinition) {
            $columns[] = $this->generateColumn($columnDefinition);
        }

        return new ColumnCollection(
            $columns,
        );
    }

    private function generateColumn(
        ColumnDefinition $columnDefinition,
    ): Column {
        return new Column(
            $columnDefinition->getLabel(),
            $columnDefinition->getAlign(),
        );
    }
}
