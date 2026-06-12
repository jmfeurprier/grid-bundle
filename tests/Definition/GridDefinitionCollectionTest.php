<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Model\Grid;

use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\Grid\Definition\GridDefinition;
use Jmf\Grid\Definition\GridDefinitionCollection;
use Jmf\Grid\Definition\KeyValueCollection;
use Jmf\Grid\Definition\RowDefinition;
use Jmf\Grid\Exception\DuplicateGridException;
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

    public function testDuplicateIdThrows(): void
    {
        $this->expectException(DuplicateGridException::class);

        new GridDefinitionCollection(
            [
                $this->gridDefinitionPrimary,
                $this->gridDefinitionPrimary,
            ],
        );
    }
}
