<?php

declare(strict_types=1);

namespace Jmf\Grid\Model;

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
