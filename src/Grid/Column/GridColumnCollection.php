<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Column;

use Webmozart\Assert\Assert;

readonly class GridColumnCollection
{
    /**
     * @param GridColumn[] $columns
     */
    public function __construct(
        private iterable $columns,
    ) {
        Assert::allIsInstanceOf($this->columns, GridColumn::class);
    }

    /**
     * @return GridColumn[]
     */
    public function all(): iterable
    {
        return $this->columns;
    }
}
