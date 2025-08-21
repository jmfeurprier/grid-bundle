<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid;

use Jmf\Grid\Grid\Column\Column;
use Jmf\Grid\Grid\Column\ColumnCollection;
use Jmf\Grid\Grid\Footer\Footer;
use Jmf\Grid\Grid\Row\Row;
use Jmf\Grid\Grid\Row\RowCollection;

readonly class Grid
{
    public function __construct(
        private ColumnCollection $columns,
        private RowCollection $rows,
        private Footer $footer,
    ) {
    }

    /**
     * @return Column[]
     */
    public function getColumns(): iterable
    {
        return $this->columns->all();
    }

    /**
     * @return Row[]
     */
    public function getRows(): iterable
    {
        return $this->rows->all();
    }

    public function getFooter(): Footer
    {
        return $this->footer;
    }
}
