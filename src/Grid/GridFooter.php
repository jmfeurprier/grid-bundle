<?php

namespace Jmf\Grid\Grid;

use Webmozart\Assert\Assert;

readonly class GridFooter
{
    /**
     * @param GridFooterRow[] $rows
     */
    public function __construct(
        private iterable $rows,
    ) {
        Assert::allIsInstanceOf($this->rows, GridFooterRow::class);
    }

    /**
     * @return GridFooterRow[]
     */
    public function getRows(): iterable
    {
        return $this->rows;
    }
}
