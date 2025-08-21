<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Column;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfiguration;

readonly class ColumnCollectionGenerator
{
    public function generate(GridConfiguration $gridConfiguration): ColumnCollection
    {
        $columns = [];

        foreach ($gridConfiguration->getColumnConfigurations() as $columnConfiguration) {
            $columns[] = $this->generateColumn($columnConfiguration);
        }

        return new ColumnCollection(
            $columns,
        );
    }

    private function generateColumn(
        ColumnConfiguration $columnConfiguration,
    ): Column {
        return new Column(
            $columnConfiguration->getLabel(),
            $columnConfiguration->getAlign(),
        );
    }
}
