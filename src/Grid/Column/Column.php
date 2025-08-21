<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Column;

readonly class Column
{
    public function __construct(
        private ?string $label,
        private ?string $align,
    ) {
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getAlign(): ?string
    {
        return $this->align;
    }
}
