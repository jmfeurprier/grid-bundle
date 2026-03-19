<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration;

use Jmf\Grid\Configuration\KeyValueCollection;
use PHPUnit\Framework\TestCase;

final class KeyValueCollectionTest extends TestCase
{
    public function testCreateEmptyReturnsEmptyCollection(): void
    {
        $collection = KeyValueCollection::createEmpty();

        self::assertSame([], $collection->all());
    }

    public function testConstructWithValuesReturnsAll(): void
    {
        $values = [
            'foo' => 'bar',
            'baz' => 42,
        ];

        $collection = new KeyValueCollection($values);

        self::assertSame($values, $collection->all());
    }
}
