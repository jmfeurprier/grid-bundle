<?php

namespace Jmf\Grid\Grid;

use Webmozart\Assert\Assert;

readonly class GridFooterRow
{
    /**
     * @param GridFooterCell[] $cells
     */
    public function __construct(
        private iterable $cells,
    ) {
        Assert::allIsInstanceOf($this->cells, GridFooterCell::class);
    }

    /**
     * @return GridFooterCell[]
     */
    public function getCells(): iterable
    {
        return $this->cells;
    }
}
