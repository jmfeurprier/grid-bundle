<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid;

use Jmf\Grid\Grid\Column\GridColumn;
use Jmf\Grid\Grid\Column\GridColumnCollection;
use Jmf\Grid\Grid\Footer\GridFooter;
use Jmf\Grid\Grid\Row\GridRow;
use Jmf\Grid\Grid\Row\GridRowCollection;

readonly class Grid
{
    public function __construct(
        private GridColumnCollection $columns,
        private GridRowCollection $rows,
        private GridFooter $footer,
    ) {
    }

    /**
     * @return GridColumn[]
     */
    public function getColumns(): iterable
    {
        return $this->columns->all();
    }

    /**
     * @return GridRow[]
     */
    public function getRows(): iterable
    {
        return $this->rows->all();
    }

    public function getFooter(): GridFooter
    {
        return $this->footer;
    }
}
