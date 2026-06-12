<?php

declare(strict_types=1);

namespace Jmf\Grid\Model;

use Webmozart\Assert\Assert;

readonly class FooterRow
{
    /**
     * @param FooterCell[] $cells
     */
    public function __construct(
        private iterable $cells,
    ) {
        Assert::allIsInstanceOf($this->cells, FooterCell::class);
    }

    /**
     * @return FooterCell[]
     */
    public function getCells(): iterable
    {
        return $this->cells;
    }
}
