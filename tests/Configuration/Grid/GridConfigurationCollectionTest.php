<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Grid;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfigurationCollection;
use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;
use Jmf\Grid\Exception\GridNotFoundException;
use Override;
use PHPUnit\Framework\TestCase;

final class GridConfigurationCollectionTest extends TestCase
{
    private GridConfiguration $gridConfigurationPrimary;

    private GridConfiguration $gridConfigurationSecondary;

    #[Override]
    protected function setUp(): void
    {
        $columnConfiguration = new ColumnConfiguration(
            null,
            'Label',
            'item.name',
            null,
        );

        $this->gridConfigurationPrimary = new GridConfiguration(
            id:                   'grid_primary',
            arguments:            [],
            gridVariables:        KeyValueCollection::createEmpty(),
            columnConfigurations: [$columnConfiguration],
            rowConfiguration:     RowConfiguration::createEmpty(),
            footerConfigurations: [],
        );

        $this->gridConfigurationSecondary = new GridConfiguration(
            id:                   'grid_secondary',
            arguments:            [],
            gridVariables:        KeyValueCollection::createEmpty(),
            columnConfigurations: [$columnConfiguration],
            rowConfiguration:     RowConfiguration::createEmpty(),
            footerConfigurations: [],
        );
    }

    public function testAllReturnsAllConfigurations(): void
    {
        $gridConfigurationCollection = new GridConfigurationCollection(
            [
                $this->gridConfigurationPrimary,
                $this->gridConfigurationSecondary,
            ],
        );

        $all = $gridConfigurationCollection->all();

        self::assertCount(2, $all);
        self::assertContains($this->gridConfigurationPrimary, $all);
        self::assertContains($this->gridConfigurationSecondary, $all);
    }

    public function testGetReturnsCorrectConfiguration(): void
    {
        $gridConfigurationCollection = new GridConfigurationCollection(
            [
                $this->gridConfigurationPrimary,
                $this->gridConfigurationSecondary,
            ],
        );

        self::assertSame($this->gridConfigurationPrimary, $gridConfigurationCollection->get('grid_primary'));
        self::assertSame($this->gridConfigurationSecondary, $gridConfigurationCollection->get('grid_secondary'));
    }

    public function testGetThrowsForUnknownId(): void
    {
        $gridConfigurationCollection = new GridConfigurationCollection(
            [
                $this->gridConfigurationPrimary,
            ],
        );

        $this->expectException(GridNotFoundException::class);

        $gridConfigurationCollection->get('unknown');
    }

    public function testEmptyCollection(): void
    {
        $gridConfigurationCollection = new GridConfigurationCollection([]);

        self::assertSame([], $gridConfigurationCollection->all());
    }
}
