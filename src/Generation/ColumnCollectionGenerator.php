<?php

declare(strict_types=1);

namespace Jmf\Grid\Generation;

use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\Grid\Definition\GridDefinition;
use Jmf\Grid\Model\Column;
use Jmf\Grid\Model\ColumnCollection;

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
