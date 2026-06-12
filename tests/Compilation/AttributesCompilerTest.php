<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Compilation;

use Jmf\Grid\Compilation\AttributesCompiler;
use PHPUnit\Framework\TestCase;

final class AttributesCompilerTest extends TestCase
{
    public function testNoAttributesYieldsEmptyCollection(): void
    {
        $collection = (new AttributesCompiler())->compile([]);

        self::assertSame([], $collection->all());
    }

    public function testMapFormIsKeptAsIs(): void
    {
        $collection = (new AttributesCompiler())->compile([
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
        $collection = (new AttributesCompiler())->compile([
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
