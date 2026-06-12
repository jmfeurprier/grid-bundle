<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Generation;

use Jmf\Grid\Definition\GridDefinition;
use Jmf\Grid\Definition\RowDefinition;
use Jmf\Grid\Generation\RowCollectionGenerator;
use Jmf\Grid\Generation\RowGenerator;
use Jmf\Grid\Definition\KeyValueCollection;
use Jmf\Grid\Model\Row;
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

    private function createGridDefinition(): GridDefinition
    {
        return new GridDefinition(
            id:                'test',
            arguments:         [],
            gridVariables:     KeyValueCollection::createEmpty(),
            columnDefinitions: [],
            rowDefinition:     RowDefinition::createEmpty(),
            footerDefinitions: [],
        );
    }

    public function testGenerateWithNoItemsReturnsEmptyCollection(): void
    {
        $gridDefinition = $this->createGridDefinition();

        $result = $this->rowCollectionGenerator->generate($gridDefinition, [], []);
        $rows   = iterator_to_array($result->all());

        self::assertSame([], $rows);
    }

    public function testGenerateWithSingleItemCallsRowGeneratorOnce(): void
    {
        $gridDefinition = $this->createGridDefinition();
        $item           = ['name' => 'Alice'];
        $expectedRow    = new Row([], null);

        $rowGenerator = $this->createMock(RowGenerator::class);
        $rowGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($gridDefinition, $item, 1, 1, [])
            ->willReturn($expectedRow)
        ;

        $result = (new RowCollectionGenerator($rowGenerator))->generate($gridDefinition, [$item], []);
        $rows   = iterator_to_array($result->all());

        self::assertCount(1, $rows);
        self::assertSame($expectedRow, $rows[0]);
    }

    public function testGenerateWithMultipleItemsPassesCorrectIndicesAndCount(): void
    {
        $gridDefinition = $this->createGridDefinition();
        $item1          = ['name' => 'Alice'];
        $item2          = ['name' => 'Bob'];
        $item3          = ['name' => 'Carol'];

        $rowGenerator = $this->createMock(RowGenerator::class);
        $rowGenerator
            ->expects(self::exactly(3))
            ->method('generate')
            ->willReturnCallback(
                function (
                    GridDefinition $config,
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
            $gridDefinition,
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
        $gridDefinition = $this->createGridDefinition();
        $arguments      = ['locale' => 'fr'];

        $rowGenerator = $this->createMock(RowGenerator::class);
        $rowGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($gridDefinition, self::anything(), self::anything(), self::anything(), $arguments)
            ->willReturn(new Row([], null))
        ;

        (new RowCollectionGenerator($rowGenerator))->generate($gridDefinition, [['name' => 'Alice']], $arguments);
    }
}
