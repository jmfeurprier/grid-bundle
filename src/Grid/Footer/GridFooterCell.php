<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Footer;

use Webmozart\Assert\Assert;

readonly class GridFooterCell
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        private string $value,
        private array $attributes,
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
}
