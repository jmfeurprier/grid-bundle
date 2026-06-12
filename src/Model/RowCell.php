<?php

declare(strict_types=1);

namespace Jmf\Grid\Model;

readonly class RowCell
{
    public function __construct(
        private string $value,
        private ?string $align,
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getAlign(): ?string
    {
        return $this->align;
    }
}
