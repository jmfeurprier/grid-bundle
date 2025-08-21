<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Column;

use Webmozart\Assert\Assert;

readonly class ColumnCollection
{
    /**
     * @param Column[] $columns
     */
    public function __construct(
        private iterable $columns,
    ) {
        Assert::allIsInstanceOf($this->columns, Column::class);
    }

    /**
     * @return Column[]
     */
    public function all(): iterable
    {
        return $this->columns;
    }
}
