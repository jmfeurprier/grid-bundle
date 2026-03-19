<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Column;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfiguration;
use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;
use Jmf\Grid\Grid\Column\Column;
use Jmf\Grid\Grid\Column\ColumnCollectionGenerator;
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
     * @param ColumnConfiguration[] $columnConfigurations
     */
    private function createGridConfiguration(array $columnConfigurations): GridConfiguration
    {
        return new GridConfiguration(
            id:                   'test',
            arguments:            [],
            gridVariables:        KeyValueCollection::createEmpty(),
            columnConfigurations: $columnConfigurations,
            rowConfiguration:     RowConfiguration::createEmpty(),
            footerConfigurations: [],
        );
    }

    public function testGenerateWithNoColumns(): void
    {
        $gridConfiguration = $this->createGridConfiguration([]);

        $result = $this->columnCollectionGenerator->generate($gridConfiguration);

        self::assertSame([], iterator_to_array($result->all()));
    }

    public function testGenerateWithSingleColumn(): void
    {
        $columnConfiguration = new ColumnConfiguration(
            align:    'left',
            label:    'Name',
            source:   'item.name',
            template: null,
        );
        $gridConfiguration = $this->createGridConfiguration([$columnConfiguration]);

        $result  = $this->columnCollectionGenerator->generate($gridConfiguration);
        $columns = iterator_to_array($result->all());

        self::assertCount(1, $columns);
        self::assertInstanceOf(Column::class, $columns[0]);
        self::assertSame('Name', $columns[0]->getLabel());
        self::assertSame('left', $columns[0]->getAlign());
    }

    public function testGenerateWithMultipleColumns(): void
    {
        $columnConfigurations = [
            new ColumnConfiguration(align: 'left',  label: 'Name',   source: 'name',   template: null),
            new ColumnConfiguration(align: 'right', label: 'Amount', source: 'amount', template: null),
            new ColumnConfiguration(align: null,    label: null,      source: null,     template: null),
        ];
        $gridConfiguration = $this->createGridConfiguration($columnConfigurations);

        $result  = $this->columnCollectionGenerator->generate($gridConfiguration);
        $columns = iterator_to_array($result->all());

        self::assertCount(3, $columns);

        self::assertSame('Name',   $columns[0]->getLabel());
        self::assertSame('left',   $columns[0]->getAlign());

        self::assertSame('Amount', $columns[1]->getLabel());
        self::assertSame('right',  $columns[1]->getAlign());

        self::assertNull($columns[2]->getLabel());
        self::assertNull($columns[2]->getAlign());
    }

    public function testGenerateWithNullLabelAndAlign(): void
    {
        $columnConfiguration = new ColumnConfiguration(
            align:    null,
            label:    null,
            source:   null,
            template: null,
        );
        $gridConfiguration = $this->createGridConfiguration([$columnConfiguration]);

        $result  = $this->columnCollectionGenerator->generate($gridConfiguration);
        $columns = iterator_to_array($result->all());

        self::assertCount(1, $columns);
        self::assertNull($columns[0]->getLabel());
        self::assertNull($columns[0]->getAlign());
    }
}
