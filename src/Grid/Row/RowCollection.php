<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Webmozart\Assert\Assert;

readonly class RowCollection
{
    /**
     * @param Row[] $rows
     */
    public function __construct(
        private iterable $rows,
    ) {
        Assert::allIsInstanceOf($this->rows, Row::class);
    }

    /**
     * @return Row[]
     */
    public function all(): iterable
    {
        return $this->rows;
    }
}
