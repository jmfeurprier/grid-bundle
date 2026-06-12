<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Attributes;

use Jmf\Grid\Grid\Row\AttributesLoader;
use PHPUnit\Framework\TestCase;

final class AttributesLoaderTest extends TestCase
{
    public function testNoAttributesYieldsEmptyCollection(): void
    {
        $collection = (new AttributesLoader())->load([]);

        self::assertSame([], $collection->all());
    }

    public function testMapFormIsKeptAsIs(): void
    {
        $collection = (new AttributesLoader())->load([
            'attributes' => [
                'data-id'   => '{{ _item.id }}',
                'data-name' => '{{ _item.name }}',
            ],
        ]);

        self::assertSame(
            [
                'data-id'   => '{{ _item.id }}',
                'data-name' => '{{ _item.name }}',
            ],
            $collection->all(),
        );
    }

    public function testXmlStyleListFormIsNormalizedToMap(): void
    {
        $collection = (new AttributesLoader())->load([
            'attributes' => [
                ['key' => 'data-id', 'value' => '{{ _item.id }}'],
                ['key' => 'data-name', 'value' => '{{ _item.name }}'],
            ],
        ]);

        self::assertSame(
            [
                'data-id'   => '{{ _item.id }}',
                'data-name' => '{{ _item.name }}',
            ],
            $collection->all(),
        );
    }
}
