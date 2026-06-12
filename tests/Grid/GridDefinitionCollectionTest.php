<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Grid;

use Jmf\Grid\Grid\Column\ColumnDefinition;
use Jmf\Grid\Grid\GridDefinition;
use Jmf\Grid\Grid\GridDefinitionCollection;
use Jmf\Grid\Grid\KeyValueCollection;
use Jmf\Grid\Grid\Row\RowDefinition;
use Jmf\Grid\Exception\GridNotFoundException;
use Override;
use PHPUnit\Framework\TestCase;

final class GridDefinitionCollectionTest extends TestCase
{
    private GridDefinition $gridDefinitionPrimary;

    private GridDefinition $gridDefinitionSecondary;

    #[Override]
    protected function setUp(): void
    {
        $columnDefinition = new ColumnDefinition(
            null,
            'Label',
            'item.name',
            null,
        );

        $this->gridDefinitionPrimary = new GridDefinition(
            id:                   'grid_primary',
            arguments:            [],
            gridVariables:        KeyValueCollection::createEmpty(),
            columnDefinitions: [$columnDefinition],
            rowDefinition:     RowDefinition::createEmpty(),
            footerDefinitions: [],
        );

        $this->gridDefinitionSecondary = new GridDefinition(
            id:                   'grid_secondary',
            arguments:            [],
            gridVariables:        KeyValueCollection::createEmpty(),
            columnDefinitions: [$columnDefinition],
            rowDefinition:     RowDefinition::createEmpty(),
            footerDefinitions: [],
        );
    }

    public function testAllReturnsAllConfigurations(): void
    {
        $gridDefinitionCollection = new GridDefinitionCollection(
            [
                $this->gridDefinitionPrimary,
                $this->gridDefinitionSecondary,
            ],
        );

        $all = $gridDefinitionCollection->all();

        self::assertCount(2, $all);
        self::assertContains($this->gridDefinitionPrimary, $all);
        self::assertContains($this->gridDefinitionSecondary, $all);
    }

    public function testGetReturnsCorrectConfiguration(): void
    {
        $gridDefinitionCollection = new GridDefinitionCollection(
            [
                $this->gridDefinitionPrimary,
                $this->gridDefinitionSecondary,
            ],
        );

        self::assertSame($this->gridDefinitionPrimary, $gridDefinitionCollection->get('grid_primary'));
        self::assertSame($this->gridDefinitionSecondary, $gridDefinitionCollection->get('grid_secondary'));
    }

    public function testGetThrowsForUnknownId(): void
    {
        $gridDefinitionCollection = new GridDefinitionCollection(
            [
                $this->gridDefinitionPrimary,
            ],
        );

        $this->expectException(GridNotFoundException::class);

        $gridDefinitionCollection->get('unknown');
    }

    public function testEmptyCollection(): void
    {
        $gridDefinitionCollection = new GridDefinitionCollection([]);

        self::assertSame([], $gridDefinitionCollection->all());
    }
}
