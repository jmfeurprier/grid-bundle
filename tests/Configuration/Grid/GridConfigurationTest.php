<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Grid;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Footer\FooterConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfiguration;
use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;
use PHPUnit\Framework\TestCase;

final class GridConfigurationTest extends TestCase
{
    public function testGetters(): void
    {
        $gridVariables        = new KeyValueCollection(['foo' => 'bar']);
        $columnConfiguration  = new ColumnConfiguration(null, 'Name', 'item.name', null);
        $rowConfiguration     = RowConfiguration::createEmpty();
        $footerConfiguration  = new FooterConfiguration(null, null, null, 'Total', null);

        $gridConfiguration = new GridConfiguration(
            id:                   'myGrid',
            arguments:            ['arg1', 'arg2'],
            gridVariables:        $gridVariables,
            columnConfigurations: [$columnConfiguration],
            rowConfiguration:     $rowConfiguration,
            footerConfigurations: [[$footerConfiguration]],
        );

        self::assertSame('myGrid', $gridConfiguration->getId());
        self::assertSame(['arg1', 'arg2'], $gridConfiguration->getArguments());
        self::assertSame($gridVariables, $gridConfiguration->getGridVariables());
        self::assertSame([$columnConfiguration], $gridConfiguration->getColumnConfigurations());
        self::assertSame($rowConfiguration, $gridConfiguration->getRowConfiguration());
        self::assertSame([[$footerConfiguration]], $gridConfiguration->getFooterConfigurations());
    }
}
