<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

readonly class GridRow
{
    /**
     * @param GridRowCell[] $cells
     */
    public function __construct(
        private iterable $cells,
        private ?string $link,
    ) {
    }

    /**
     * @return GridRowCell[]
     */
    public function getCells(): iterable
    {
        return $this->cells;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }
}
