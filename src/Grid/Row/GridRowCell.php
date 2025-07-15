<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Webmozart\Assert\Assert;

readonly class GridRowCell
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function __construct(
        private string $value,
        private array $parameters,
    ) {
        Assert::isMap($this->parameters);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
