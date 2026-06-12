<?php

declare(strict_types=1);

namespace Jmf\Grid\Model;

use Webmozart\Assert\Assert;

readonly class FooterCell
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        private string $value,
        private array $attributes,
        private ?string $align,
    ) {
        Assert::isMap($this->attributes);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAlign(): ?string
    {
        return $this->align;
    }
}
