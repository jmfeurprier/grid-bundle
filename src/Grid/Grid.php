<?php

namespace Jmf\Grid\Grid;

use Webmozart\Assert\Assert;

readonly class Grid
{
    /**
     * @param GridColumn[] $columns
     * @param GridRow[]    $rows
     */
    public function __construct(
        private iterable $columns,
        private iterable $rows,
        private GridFooter $footer,
    ) {
        Assert::allIsInstanceOf($this->columns, GridColumn::class);
        Assert::allIsInstanceOf($this->rows, GridRow::class);
    }

    /**
     * @return GridColumn[]
     */
    public function getColumns(): iterable
    {
        return $this->columns;
    }

    /**
     * @return GridRow[]
     */
    public function getRows(): iterable
    {
        return $this->rows;
    }

    public function getFooter(): GridFooter
    {
        return $this->footer;
    }
}
