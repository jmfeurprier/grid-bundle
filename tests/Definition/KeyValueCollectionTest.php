<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Definition;

use Jmf\Grid\Definition\KeyValueCollection;
use PHPUnit\Framework\TestCase;

final class KeyValueCollectionTest extends TestCase
{
    public function testCreateEmptyReturnsEmptyCollection(): void
    {
        $keyValueCollection = KeyValueCollection::createEmpty();

        self::assertSame([], $keyValueCollection->all());
    }

    public function testConstructWithValuesReturnsAll(): void
    {
        $values = [
            'foo' => 'bar',
            'baz' => 42,
        ];

        $keyValueCollection = new KeyValueCollection($values);

        self::assertSame($values, $keyValueCollection->all());
    }
}
