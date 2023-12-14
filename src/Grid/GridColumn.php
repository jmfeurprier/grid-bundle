<?php

namespace Jmf\Grid\Grid;

readonly class GridColumn
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
