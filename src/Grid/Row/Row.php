<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

readonly class Row
{
    /**
     * @param RowCell[]             $cells
     * @param array<string, string> $attributes
     */
    public function __construct(
        private iterable $cells,
        private ?string $link,
        private array $attributes = [],
    ) {
    }

    /**
     * @return RowCell[]
     */
    public function getCells(): iterable
    {
        return $this->cells;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @return array<string, string>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
