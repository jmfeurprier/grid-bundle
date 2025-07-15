<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Webmozart\Assert\Assert;

readonly class GridRowCollection
{
    /**
     * @param GridRow[] $rows
     */
    public function __construct(
        private iterable $rows,
    ) {
        Assert::allIsInstanceOf($this->rows, GridRow::class);
    }

    /**
     * @return GridRow[]
     */
    public function all(): iterable
    {
        return $this->rows;
    }
}
