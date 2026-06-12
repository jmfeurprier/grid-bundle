<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Compilation;

use Jmf\Grid\Compilation\AttributesCompiler;
use Jmf\Grid\Compilation\ColumnDefinitionCompiler;
use Jmf\Grid\Compilation\FooterDefinitionCompiler;
use Jmf\Grid\Compilation\GridDefinitionCompiler;
use Jmf\Grid\Compilation\RowDefinitionCompiler;
use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\Grid\Definition\FooterDefinition;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\Grid\Compilation\PresetApplier;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Override;
use PHPUnit\Framework\TestCase;

final class GridDefinitionCompilerTest extends TestCase
{
    private GridDefinitionCompiler $gridDefinitionCompiler;

    #[Override]
    protected function setUp(): void
    {
        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetApplier    = new PresetApplier($presetRepository);

        $this->gridDefinitionCompiler = new GridDefinitionCompiler(
            columnDefinitionCompiler: new ColumnDefinitionCompiler($presetApplier),
            rowDefinitionCompiler:    new RowDefinitionCompiler(
                                          new AttributesCompiler(),
                                      ),
            footerDefinitionCompiler: new FooterDefinitionCompiler($presetApplier),
        );
    }

    public function testCompileMinimalConfigWithOneColumn(): void
    {
        $config = [
            'columns' => [
                [
                    'source' => 'item.name',
                ],
            ],
        ];

        $result = $this->gridDefinitionCompiler->compile('myGrid', $config);

        self::assertSame('myGrid', $result->getId());
        self::assertSame([], $result->getArguments());
        self::assertSame([], $result->getGridVariables()->all());

        $columns = iterator_to_array($result->getColumnDefinitions());

        self::assertCount(1, $columns);

        $column = $columns[0];

        self::assertInstanceOf(ColumnDefinition::class, $column);
        self::assertSame('item.name', $column->getSource());
    }

    public function testCompileWithArguments(): void
    {
        $config = [
            'grid'    => [
                'arguments' => [
                    'arg1',
                    'arg2',
                ],
            ],
            'columns' => [
                ['source' => 'item.name'],
            ],
        ];

        $result = $this->gridDefinitionCompiler->compile('myGrid', $config);

        self::assertSame(
            [
                'arg1',
                'arg2',
            ],
            $result->getArguments(),
        );
    }

    public function testCompileWithGridVariables(): void
    {
        $config = [
            'grid'    => ['variables' => ['foo' => 'bar']],
            'columns' => [
                ['source' => 'item.name'],
            ],
        ];

        $result = $this->gridDefinitionCompiler->compile('myGrid', $config);

        self::assertSame(['foo' => 'bar'], $result->getGridVariables()->all());
    }

    public function testCompileWithRowConfig(): void
    {
        $config = [
            'columns' => [
                ['source' => 'item.name'],
            ],
            'rows'    => ['link' => 'my_route'],
        ];

        $result = $this->gridDefinitionCompiler->compile('myGrid', $config);

        self::assertSame('my_route', $result->getRowDefinition()->getLink());
    }

    public function testCompileWithFooter(): void
    {
        $config = [
            'columns' => [
                ['source' => 'item.name'],
            ],
            'footer'  => [
                [
                    ['value' => 'Total'],
                ],
            ],
        ];

        $result = $this->gridDefinitionCompiler->compile('myGrid', $config);

        $footerRows = iterator_to_array($result->getFooterDefinitions());
        self::assertCount(1, $footerRows);
        self::assertCount(1, $footerRows[0]);
        self::assertInstanceOf(FooterDefinition::class, $footerRows[0][0]);
        self::assertSame('Total', $footerRows[0][0]->getValue());
    }

    public function testCompileWithoutColumnsThrows(): void
    {
        $this->expectException(GridWithoutColumnException::class);

        $this->gridDefinitionCompiler->compile('myGrid', []);
    }

    public function testCompileWithEmptyColumnsThrows(): void
    {
        $this->expectException(GridWithoutColumnException::class);

        $this->gridDefinitionCompiler->compile('myGrid', ['columns' => []]);
    }

    public function testCompileReturnsEmptyRowConfigWhenNoRowsKey(): void
    {
        $config = [
            'columns' => [
                ['source' => 'item.name'],
            ],
        ];

        $result = $this->gridDefinitionCompiler->compile('myGrid', $config);

        self::assertNull($result->getRowDefinition()->getLink());
        self::assertSame([], $result->getRowDefinition()->getVariables()->all());
    }

    public function testCompileReturnsEmptyFooterWhenNoFooterKey(): void
    {
        $config = [
            'columns' => [
                ['source' => 'item.name'],
            ],
        ];

        $result = $this->gridDefinitionCompiler->compile('myGrid', $config);

        self::assertSame([], iterator_to_array($result->getFooterDefinitions()));
    }
}
