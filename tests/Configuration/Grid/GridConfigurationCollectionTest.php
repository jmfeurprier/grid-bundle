<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Grid;

use Override;
use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfigurationCollection;
use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;
use Jmf\Grid\Exception\GridNotFoundException;
use PHPUnit\Framework\TestCase;

final class GridConfigurationCollectionTest extends TestCase
{
    private GridConfiguration $gridConfigA;

    private GridConfiguration $gridConfigB;

    #[Override]
    protected function setUp(): void
    {
        $colConfig = new ColumnConfiguration(null, 'Label', 'item.name', null);

        $this->gridConfigA = new GridConfiguration(
            id:                   'gridA',
            arguments:            [],
            gridVariables:        KeyValueCollection::createEmpty(),
            columnConfigurations: [$colConfig],
            rowConfiguration:     RowConfiguration::createEmpty(),
            footerConfigurations: [],
        );

        $this->gridConfigB = new GridConfiguration(
            id:                   'gridB',
            arguments:            [],
            gridVariables:        KeyValueCollection::createEmpty(),
            columnConfigurations: [$colConfig],
            rowConfiguration:     RowConfiguration::createEmpty(),
            footerConfigurations: [],
        );
    }

    public function testAllReturnsAllConfigurations(): void
    {
        $collection =
            new GridConfigurationCollection(
                [
                    $this->gridConfigA,
                    $this->gridConfigB,
                ],
            );

        $all = $collection->all();

        self::assertCount(2, $all);
        self::assertContains($this->gridConfigA, $all);
        self::assertContains($this->gridConfigB, $all);
    }

    public function testGetReturnsCorrectConfiguration(): void
    {
        $collection =
            new GridConfigurationCollection(
                [
                    $this->gridConfigA,
                    $this->gridConfigB,
                ],
            );

        self::assertSame($this->gridConfigA, $collection->get('gridA'));
        self::assertSame($this->gridConfigB, $collection->get('gridB'));
    }

    public function testGetThrowsForUnknownId(): void
    {
        $collection = new GridConfigurationCollection([$this->gridConfigA]);

        $this->expectException(GridNotFoundException::class);

        $collection->get('unknown');
    }

    public function testEmptyCollection(): void
    {
        $collection = new GridConfigurationCollection([]);

        self::assertSame([], $collection->all());
    }
}
