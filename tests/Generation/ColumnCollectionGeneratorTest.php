<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Generation;

use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\Grid\Definition\GridDefinition;
use Jmf\Grid\Definition\RowDefinition;
use Jmf\Grid\Generation\ColumnCollectionGenerator;
use Jmf\Grid\Model\Column;
use Jmf\Grid\Definition\KeyValueCollection;
use Override;
use PHPUnit\Framework\TestCase;

final class ColumnCollectionGeneratorTest extends TestCase
{
    private ColumnCollectionGenerator $columnCollectionGenerator;

    #[Override]
    protected function setUp(): void
    {
        $this->columnCollectionGenerator = new ColumnCollectionGenerator();
    }

    /**
     * @param ColumnDefinition[] $columnDefinitions
     */
    private function createGridDefinition(array $columnDefinitions): GridDefinition
    {
        return new GridDefinition(
            id:                'test',
            arguments:         [],
            gridVariables:     KeyValueCollection::createEmpty(),
            columnDefinitions: $columnDefinitions,
            rowDefinition:     RowDefinition::createEmpty(),
            footerDefinitions: [],
        );
    }

    public function testGenerateWithNoColumns(): void
    {
        $gridDefinition = $this->createGridDefinition([]);

        $result = $this->columnCollectionGenerator->generate($gridDefinition);

        self::assertSame([], iterator_to_array($result->all()));
    }

    public function testGenerateWithSingleColumn(): void
    {
        $columnDefinition = new ColumnDefinition(
            align:    'left',
            label:    'Name',
            source:   'item.name',
            template: null,
        );
        $gridDefinition   = $this->createGridDefinition([$columnDefinition]);

        $result  = $this->columnCollectionGenerator->generate($gridDefinition);
        $columns = iterator_to_array($result->all());

        self::assertCount(1, $columns);
        self::assertInstanceOf(Column::class, $columns[0]);
        self::assertSame('Name', $columns[0]->getLabel());
        self::assertSame('left', $columns[0]->getAlign());
    }

    public function testGenerateWithMultipleColumns(): void
    {
        $columnDefinitions = [
            new ColumnDefinition(align: 'left', label: 'Name', source: 'name', template: null),
            new ColumnDefinition(align: 'right', label: 'Amount', source: 'amount', template: null),
            new ColumnDefinition(align: null, label: null, source: null, template: null),
        ];
        $gridDefinition    = $this->createGridDefinition($columnDefinitions);

        $result  = $this->columnCollectionGenerator->generate($gridDefinition);
        $columns = iterator_to_array($result->all());

        self::assertCount(3, $columns);

        self::assertSame('Name', $columns[0]->getLabel());
        self::assertSame('left', $columns[0]->getAlign());

        self::assertSame('Amount', $columns[1]->getLabel());
        self::assertSame('right', $columns[1]->getAlign());

        self::assertNull($columns[2]->getLabel());
        self::assertNull($columns[2]->getAlign());
    }

    public function testGenerateWithNullLabelAndAlign(): void
    {
        $columnDefinition = new ColumnDefinition(
            align:    null,
            label:    null,
            source:   null,
            template: null,
        );
        $gridDefinition   = $this->createGridDefinition([$columnDefinition]);

        $result  = $this->columnCollectionGenerator->generate($gridDefinition);
        $columns = iterator_to_array($result->all());

        self::assertCount(1, $columns);
        self::assertNull($columns[0]->getLabel());
        self::assertNull($columns[0]->getAlign());
    }
}
