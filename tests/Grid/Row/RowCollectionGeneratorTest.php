<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Row;

use Jmf\Grid\Configuration\Grid\GridConfiguration;
use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;
use Jmf\Grid\Grid\Row\Row;
use Jmf\Grid\Grid\Row\RowCollectionGenerator;
use Jmf\Grid\Grid\Row\RowGenerator;
use Override;
use PHPUnit\Framework\TestCase;

final class RowCollectionGeneratorTest extends TestCase
{
    private RowCollectionGenerator $rowCollectionGenerator;

    #[Override]
    protected function setUp(): void
    {
        $this->rowCollectionGenerator = new RowCollectionGenerator(
            $this->createStub(RowGenerator::class),
        );
    }

    private function createGridConfiguration(): GridConfiguration
    {
        return new GridConfiguration(
            id:                   'test',
            arguments:            [],
            gridVariables:        KeyValueCollection::createEmpty(),
            columnConfigurations: [],
            rowConfiguration:     RowConfiguration::createEmpty(),
            footerConfigurations: [],
        );
    }

    public function testGenerateWithNoItemsReturnsEmptyCollection(): void
    {
        $gridConfiguration = $this->createGridConfiguration();

        $result = $this->rowCollectionGenerator->generate($gridConfiguration, [], []);
        $rows   = iterator_to_array($result->all());

        self::assertSame([], $rows);
    }

    public function testGenerateWithSingleItemCallsRowGeneratorOnce(): void
    {
        $gridConfiguration = $this->createGridConfiguration();
        $item              = ['name' => 'Alice'];
        $expectedRow       = new Row([], null);

        $rowGenerator = $this->createMock(RowGenerator::class);
        $rowGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($gridConfiguration, $item, 1, 1, [])
            ->willReturn($expectedRow)
        ;

        $result = (new RowCollectionGenerator($rowGenerator))->generate($gridConfiguration, [$item], []);
        $rows   = iterator_to_array($result->all());

        self::assertCount(1, $rows);
        self::assertSame($expectedRow, $rows[0]);
    }

    public function testGenerateWithMultipleItemsPassesCorrectIndicesAndCount(): void
    {
        $gridConfiguration = $this->createGridConfiguration();
        $item1             = ['name' => 'Alice'];
        $item2             = ['name' => 'Bob'];
        $item3             = ['name' => 'Carol'];

        $rowGenerator = $this->createMock(RowGenerator::class);
        $rowGenerator
            ->expects(self::exactly(3))
            ->method('generate')
            ->willReturnCallback(
                function (
                    GridConfiguration $config,
                    array $item,
                    int $index,
                    int $count,
                ): Row {
                    self::assertSame(3, $count);

                    return new Row([], null);
                },
            )
        ;

        $result = (new RowCollectionGenerator($rowGenerator))->generate(
            $gridConfiguration,
            [
                $item1,
                $item2,
                $item3,
            ],
            [],
        );

        self::assertCount(3, iterator_to_array($result->all()));
    }

    public function testGeneratePassesArgumentsToRowGenerator(): void
    {
        $gridConfiguration = $this->createGridConfiguration();
        $arguments         = ['locale' => 'fr'];

        $rowGenerator = $this->createMock(RowGenerator::class);
        $rowGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($gridConfiguration, self::anything(), self::anything(), self::anything(), $arguments)
            ->willReturn(new Row([], null))
        ;

        (new RowCollectionGenerator($rowGenerator))->generate($gridConfiguration, [['name' => 'Alice']], $arguments);
    }
}
