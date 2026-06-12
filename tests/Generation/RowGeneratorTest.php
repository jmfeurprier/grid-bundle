<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Generation;

use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\Grid\Definition\GridDefinition;
use Jmf\Grid\Definition\RowDefinition;
use Jmf\Grid\Generation\RowCellGenerator;
use Jmf\Grid\Generation\RowGenerator;
use Jmf\Grid\Generation\RowLinkGenerator;
use Jmf\Grid\Definition\KeyValueCollection;
use Jmf\Grid\Model\Row;
use Jmf\Grid\Model\RowCell;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Override;
use PHPUnit\Framework\TestCase;

final class RowGeneratorTest extends TestCase
{
    private TemplateRendererInterface $templateRenderer;

    private RowCellGenerator $rowCellGenerator;

    private RowLinkGenerator $rowLinkGenerator;

    private RowGenerator $rowGenerator;

    #[Override]
    protected function setUp(): void
    {
        $this->templateRenderer = $this->createStub(TemplateRendererInterface::class);
        $this->rowCellGenerator = $this->createStub(RowCellGenerator::class);
        $this->rowLinkGenerator = $this->createStub(RowLinkGenerator::class);

        $this->rowGenerator = new RowGenerator(
            $this->templateRenderer,
            $this->rowCellGenerator,
            $this->rowLinkGenerator,
        );
    }

    /**
     * @param ColumnDefinition[] $columnDefinitions
     */
    private function createGridDefinition(
        array $columnDefinitions = [],
        ?RowDefinition $rowDefinition = null,
    ): GridDefinition {
        return new GridDefinition(
            id:                   'test',
            arguments:            [],
            gridVariables:        KeyValueCollection::createEmpty(),
            columnDefinitions: $columnDefinitions,
            rowDefinition:     $rowDefinition ?? RowDefinition::createEmpty(),
            footerDefinitions: [],
        );
    }

    public function testGenerateWithNoColumnsReturnsEmptyRow(): void
    {
        $gridDefinition = $this->createGridDefinition();

        $row = $this->rowGenerator->generate($gridDefinition, [], 1, 1, []);

        self::assertInstanceOf(Row::class, $row);
        self::assertSame([], iterator_to_array($row->getCells()));
    }

    public function testGenerateWithNoLinkReturnsNullLink(): void
    {
        $gridDefinition = $this->createGridDefinition();

        $row = $this->rowGenerator->generate($gridDefinition, [], 1, 1, []);

        self::assertNull($row->getLink());
    }

    public function testGenerateWithLinkSetsLink(): void
    {
        $gridDefinition = $this->createGridDefinition();

        $this->rowLinkGenerator = $this->createMock(RowLinkGenerator::class);
        $this->rowLinkGenerator
            ->expects(self::once())
            ->method('generate')
            ->willReturn('/items/42');

        $rowGenerator = new RowGenerator(
            $this->templateRenderer,
            $this->rowCellGenerator,
            $this->rowLinkGenerator,
        );

        $row = $rowGenerator->generate($gridDefinition, [], 1, 1, []);

        self::assertSame('/items/42', $row->getLink());
    }

    public function testGenerateWithColumnsBuildsCells(): void
    {
        $columnConfig      = new ColumnDefinition(align: null, label: 'Name', source: 'name', template: null);
        $gridDefinition = $this->createGridDefinition([$columnConfig]);
        $expectedCell      = new RowCell('Alice', null);

        $rowCellGenerator = $this->createMock(RowCellGenerator::class);
        $rowCellGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($columnConfig, self::anything(), self::anything())
            ->willReturn($expectedCell);

        $rowGenerator = new RowGenerator(
            $this->templateRenderer,
            $rowCellGenerator,
            $this->rowLinkGenerator,
        );

        $row   = $rowGenerator->generate($gridDefinition, [], 1, 1, []);
        $cells = iterator_to_array($row->getCells());

        self::assertCount(1, $cells);
        self::assertSame($expectedCell, $cells[0]);
    }

    public function testGenerateBuildsRowAttributes(): void
    {
        $rowDefinition = new RowDefinition(
            null,
            KeyValueCollection::createEmpty(),
            new KeyValueCollection(['class' => 'row-{{ _loop.index }}']),
        );
        $gridDefinition = $this->createGridDefinition([], $rowDefinition);

        $templateRenderer = $this->createMock(TemplateRendererInterface::class);
        $templateRenderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with('row-{{ _loop.index }}', self::anything())
            ->willReturn('row-1');

        $rowGenerator = new RowGenerator(
            $templateRenderer,
            $this->rowCellGenerator,
            $this->rowLinkGenerator,
        );

        $row = $rowGenerator->generate($gridDefinition, [], 1, 3, []);

        self::assertSame(['class' => 'row-1'], $row->getAttributes());
    }

    public function testGenerateWithNoAttributesReturnsEmptyAttributes(): void
    {
        $gridDefinition = $this->createGridDefinition();

        $row = $this->rowGenerator->generate($gridDefinition, [], 1, 1, []);

        self::assertSame([], $row->getAttributes());
    }

    public function testGenerateCustomRowVariablesAreRendered(): void
    {
        $rowDefinition = new RowDefinition(
            null,
            new KeyValueCollection(['label' => 'Item: {{ _item.name }}']),
            KeyValueCollection::createEmpty(),
        );
        $gridDefinition = $this->createGridDefinition([], $rowDefinition);

        $templateRenderer = $this->createMock(TemplateRendererInterface::class);
        $templateRenderer
            ->expects(self::once())
            ->method('renderFromString')
            ->with('Item: {{ _item.name }}', self::anything())
            ->willReturn('Item: Alice');

        $rowGenerator = new RowGenerator(
            $templateRenderer,
            $this->rowCellGenerator,
            $this->rowLinkGenerator,
        );

        $rowGenerator->generate($gridDefinition, ['name' => 'Alice'], 1, 1, []);
    }
}
