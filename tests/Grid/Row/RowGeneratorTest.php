<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Row;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfiguration;
use Jmf\Grid\Configuration\KeyValueCollection;
use Jmf\Grid\Configuration\Row\RowConfiguration;
use Jmf\Grid\Grid\Row\Row;
use Jmf\Grid\Grid\Row\RowCell;
use Jmf\Grid\Grid\Row\RowCellGenerator;
use Jmf\Grid\Grid\Row\RowGenerator;
use Jmf\Grid\Grid\Row\RowLinkGenerator;
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
     * @param ColumnConfiguration[] $columnConfigurations
     */
    private function createGridConfiguration(
        array $columnConfigurations = [],
        ?RowConfiguration $rowConfiguration = null,
    ): GridConfiguration {
        return new GridConfiguration(
            id:                   'test',
            arguments:            [],
            gridVariables:        KeyValueCollection::createEmpty(),
            columnConfigurations: $columnConfigurations,
            rowConfiguration:     $rowConfiguration ?? RowConfiguration::createEmpty(),
            footerConfigurations: [],
        );
    }

    public function testGenerateWithNoColumnsReturnsEmptyRow(): void
    {
        $gridConfiguration = $this->createGridConfiguration();

        $row = $this->rowGenerator->generate($gridConfiguration, [], 1, 1, []);

        self::assertInstanceOf(Row::class, $row);
        self::assertSame([], iterator_to_array($row->getCells()));
    }

    public function testGenerateWithNoLinkReturnsNullLink(): void
    {
        $gridConfiguration = $this->createGridConfiguration();

        $row = $this->rowGenerator->generate($gridConfiguration, [], 1, 1, []);

        self::assertNull($row->getLink());
    }

    public function testGenerateWithLinkSetsLink(): void
    {
        $gridConfiguration = $this->createGridConfiguration();

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

        $row = $rowGenerator->generate($gridConfiguration, [], 1, 1, []);

        self::assertSame('/items/42', $row->getLink());
    }

    public function testGenerateWithColumnsBuildsCells(): void
    {
        $columnConfig      = new ColumnConfiguration(align: null, label: 'Name', source: 'name', template: null);
        $gridConfiguration = $this->createGridConfiguration([$columnConfig]);
        $expectedCell      = new RowCell('Alice', []);

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

        $row   = $rowGenerator->generate($gridConfiguration, [], 1, 1, []);
        $cells = iterator_to_array($row->getCells());

        self::assertCount(1, $cells);
        self::assertSame($expectedCell, $cells[0]);
    }

    public function testGenerateBuildsRowAttributes(): void
    {
        $rowConfiguration = new RowConfiguration(
            null,
            KeyValueCollection::createEmpty(),
            new KeyValueCollection(['class' => 'row-{{ _loop.index }}']),
        );
        $gridConfiguration = $this->createGridConfiguration([], $rowConfiguration);

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

        $row = $rowGenerator->generate($gridConfiguration, [], 1, 3, []);

        self::assertSame(['class' => 'row-1'], $row->getAttributes());
    }

    public function testGenerateWithNoAttributesReturnsEmptyAttributes(): void
    {
        $gridConfiguration = $this->createGridConfiguration();

        $row = $this->rowGenerator->generate($gridConfiguration, [], 1, 1, []);

        self::assertSame([], $row->getAttributes());
    }

    public function testGenerateCustomRowVariablesAreRendered(): void
    {
        $rowConfiguration = new RowConfiguration(
            null,
            new KeyValueCollection(['label' => 'Item: {{ _item.name }}']),
            KeyValueCollection::createEmpty(),
        );
        $gridConfiguration = $this->createGridConfiguration([], $rowConfiguration);

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

        $rowGenerator->generate($gridConfiguration, ['name' => 'Alice'], 1, 1, []);
    }
}
