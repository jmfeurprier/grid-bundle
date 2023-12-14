<?php

namespace Jmf\Grid\Configuration;

use Webmozart\Assert\Assert;

readonly class KeyValueCollection
{
    public static function createEmpty(): self
    {
        return new self([]);
    }

    /**
     * @param array<string, mixed> $values
     */
    public function __construct(
        private array $values,
    ) {
        Assert::isMap($this->values);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->values;
    }
}
