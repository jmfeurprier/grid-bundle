<?php

namespace Jmf\Grid\Grid;

use Jmf\Grid\Configuration\ColumnConfiguration;
use Jmf\Grid\Configuration\GridConfiguration;

readonly class GridColumnsGenerator
{
    /**
     * @return GridColumn[]
     */
    public function generate(GridConfiguration $gridConfiguration): iterable
    {
        $columns = [];

        foreach ($gridConfiguration->getColumnConfigurations() as $columnConfiguration) {
            $columns[] = $this->generateColumn($columnConfiguration);
        }

        return $columns;
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
