<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration;

use Jmf\Grid\Exception\GridException;
use Webmozart\Assert\Assert;

/**
 * @psalm-template T of object
 */
readonly class KeyObjectCollection
{
    /**
     * @psalm-param array<string, T> $values
     * @psalm-param class-string<T>  $class
     */
    public function __construct(
        private array $values,
        string $class,
    ) {
        Assert::isMap($this->values);
        Assert::allIsInstanceOf($this->values, $class);
    }

    /**
     * @psalm-param non-empty-string $key
     *
     * @psalm-return T
     *
     * @throws GridException
     */
    public function get(string $key): object
    {
        return $this->values[$key] ?? throw new GridException();
    }
}
