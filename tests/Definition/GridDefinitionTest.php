<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Model\Grid;

use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\Grid\Definition\FooterDefinition;
use Jmf\Grid\Definition\GridDefinition;
use Jmf\Grid\Definition\KeyValueCollection;
use Jmf\Grid\Definition\RowDefinition;
use PHPUnit\Framework\TestCase;

final class GridDefinitionTest extends TestCase
{
    public function testGetters(): void
    {
        $gridVariables        = new KeyValueCollection(['foo' => 'bar']);
        $columnDefinition  = new ColumnDefinition(null, 'Name', 'item.name', null);
        $rowDefinition     = RowDefinition::createEmpty();
        $footerDefinition  = new FooterDefinition(null, null, null, 'Total', null);

        $gridDefinition = new GridDefinition(
            id:                   'myGrid',
            arguments:            ['arg1', 'arg2'],
            gridVariables:        $gridVariables,
            columnDefinitions: [$columnDefinition],
            rowDefinition:     $rowDefinition,
            footerDefinitions: [[$footerDefinition]],
        );

        self::assertSame('myGrid', $gridDefinition->getId());
        self::assertSame(['arg1', 'arg2'], $gridDefinition->getArguments());
        self::assertSame($gridVariables, $gridDefinition->getGridVariables());
        self::assertSame([$columnDefinition], $gridDefinition->getColumnDefinitions());
        self::assertSame($rowDefinition, $gridDefinition->getRowDefinition());
        self::assertSame([[$footerDefinition]], $gridDefinition->getFooterDefinitions());
    }
}
