<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Row;

use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;
use PHPUnit\Framework\TestCase;

final class RowConfigurationTest extends TestCase
{
    public function testCreateEmptyReturnsDefaults(): void
    {
        $rowConfiguration = RowConfiguration::createEmpty();

        self::assertNull($rowConfiguration->getLink());
        self::assertSame([], $rowConfiguration->getVariables()->all());
        self::assertSame([], $rowConfiguration->getAttributes()->all());
    }

    public function testGetters(): void
    {
        $variables  = new KeyValueCollection(['route' => 'my_route']);
        $attributes = new KeyValueCollection(['class' => 'highlight']);

        $rowConfiguration = new RowConfiguration(
            link:       'my_route',
            variables:  $variables,
            attributes: $attributes,
        );

        self::assertSame('my_route', $rowConfiguration->getLink());
        self::assertSame($variables, $rowConfiguration->getVariables());
        self::assertSame($attributes, $rowConfiguration->getAttributes());
    }

    public function testGettersWithNullLink(): void
    {
        $rowConfiguration = new RowConfiguration(
            link:       null,
            variables:  KeyValueCollection::createEmpty(),
            attributes: KeyValueCollection::createEmpty(),
        );

        self::assertNull($rowConfiguration->getLink());
    }
}
