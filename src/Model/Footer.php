<?php

declare(strict_types=1);

namespace Jmf\Grid\Model;

use Webmozart\Assert\Assert;

readonly class Footer
{
    /**
     * @param FooterRow[] $rows
     */
    public function __construct(
        private iterable $rows,
    ) {
        Assert::allIsInstanceOf($this->rows, FooterRow::class);
    }

    /**
     * @return FooterRow[]
     */
    public function getRows(): iterable
    {
        return $this->rows;
    }
}
