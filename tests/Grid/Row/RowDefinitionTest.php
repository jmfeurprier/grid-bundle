<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Row;

use Jmf\Grid\Grid\KeyValueCollection;
use Jmf\Grid\Grid\Row\RowDefinition;
use PHPUnit\Framework\TestCase;

final class RowDefinitionTest extends TestCase
{
    public function testCreateEmptyReturnsDefaults(): void
    {
        $rowDefinition = RowDefinition::createEmpty();

        self::assertNull($rowDefinition->getLink());
        self::assertSame([], $rowDefinition->getVariables()->all());
        self::assertSame([], $rowDefinition->getAttributes()->all());
    }

    public function testGetters(): void
    {
        $variables  = new KeyValueCollection(['route' => 'my_route']);
        $attributes = new KeyValueCollection(['class' => 'highlight']);

        $rowDefinition = new RowDefinition(
            link:       'my_route',
            variables:  $variables,
            attributes: $attributes,
        );

        self::assertSame('my_route', $rowDefinition->getLink());
        self::assertSame($variables, $rowDefinition->getVariables());
        self::assertSame($attributes, $rowDefinition->getAttributes());
    }

    public function testGettersWithNullLink(): void
    {
        $rowDefinition = new RowDefinition(
            link:       null,
            variables:  KeyValueCollection::createEmpty(),
            attributes: KeyValueCollection::createEmpty(),
        );

        self::assertNull($rowDefinition->getLink());
    }
}
