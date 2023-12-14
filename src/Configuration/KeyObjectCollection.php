<?php

namespace Jmf\Grid\Configuration;

use DomainException;
use Webmozart\Assert\Assert;

/**
 * @template T of object
 */
readonly class KeyObjectCollection
{
    /**
     * @param array<string, T> $values
     * @param class-string<T>  $class
     */
    public function __construct(
        private array $values,
        string $class,
    ) {
        Assert::isMap($this->values);
        Assert::allIsInstanceOf($this->values, $class);
    }

    /**
     * @return T
     */
    public function get(string $key): object
    {
        return $this->values[$key] ?? throw new DomainException();
    }
}
