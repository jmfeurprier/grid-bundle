<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Column;

use Jmf\Grid\Configuration\ColumnConfiguration;
use Jmf\Grid\Configuration\GridConfiguration;

readonly class GridColumnsGenerator
{
    public function generate(GridConfiguration $gridConfiguration): GridColumnCollection
    {
        $columns = [];

        foreach ($gridConfiguration->getColumnConfigurations() as $columnConfiguration) {
            $columns[] = $this->generateColumn($columnConfiguration);
        }

        return new GridColumnCollection(
            $columns,
        );
    }

    private function generateColumn(
        ColumnConfiguration $columnConfiguration,
    ): GridColumn {
        return new GridColumn(
            $columnConfiguration->getLabel(),
            $columnConfiguration->getAlign(),
        );
    }
}
