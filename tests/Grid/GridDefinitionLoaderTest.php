<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Grid;

use Jmf\Grid\Grid\Row\AttributesLoader;
use Jmf\Grid\Grid\Column\ColumnDefinition;
use Jmf\Grid\Grid\Column\ColumnDefinitionLoader;
use Jmf\Grid\Grid\Footer\FooterDefinition;
use Jmf\Grid\Grid\Footer\FooterDefinitionLoader;
use Jmf\Grid\Grid\GridDefinitionLoader;
use Jmf\Grid\Grid\Preset\PresetApplier;
use Jmf\Grid\Grid\Row\RowDefinitionLoader;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Override;
use PHPUnit\Framework\TestCase;

final class GridDefinitionLoaderTest extends TestCase
{
    private GridDefinitionLoader $loader;

    #[Override]
    protected function setUp(): void
    {
        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetApplier    = new PresetApplier($presetRepository);

        $this->loader = new GridDefinitionLoader(
            columnDefinitionLoader: new ColumnDefinitionLoader($presetApplier),
            rowDefinitionLoader:    new RowDefinitionLoader(
                                           new AttributesLoader(),
                                       ),
            footerDefinitionLoader: new FooterDefinitionLoader($presetApplier),
        );
    }

    public function testLoadMinimalConfigWithOneColumn(): void
    {
        $config = [
            'columns' => [
                [
                    'source' => 'item.name',
                ],
            ],
        ];

        $result = $this->loader->load('myGrid', $config);

        self::assertSame('myGrid', $result->getId());
        self::assertSame([], $result->getArguments());
        self::assertSame([], $result->getGridVariables()->all());

        $columns = iterator_to_array($result->getColumnDefinitions());

        self::assertCount(1, $columns);

        $column = $columns[0];

        self::assertInstanceOf(ColumnDefinition::class, $column);
        self::assertSame('item.name', $column->getSource());
    }

    public function testLoadWithArguments(): void
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

        $result = $this->loader->load('myGrid', $config);

        self::assertSame(
            [
                'arg1',
                'arg2',
            ],
            $result->getArguments(),
        );
    }

    public function testLoadWithGridVariables(): void
    {
        $config = [
            'grid'    => ['variables' => ['foo' => 'bar']],
            'columns' => [
                ['source' => 'item.name'],
            ],
        ];

        $result = $this->loader->load('myGrid', $config);

        self::assertSame(['foo' => 'bar'], $result->getGridVariables()->all());
    }

    public function testLoadWithRowConfig(): void
    {
        $config = [
            'columns' => [
                ['source' => 'item.name'],
            ],
            'rows'    => ['link' => 'my_route'],
        ];

        $result = $this->loader->load('myGrid', $config);

        self::assertSame('my_route', $result->getRowDefinition()->getLink());
    }

    public function testLoadWithFooter(): void
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

        $result = $this->loader->load('myGrid', $config);

        $footerRows = iterator_to_array($result->getFooterDefinitions());
        self::assertCount(1, $footerRows);
        self::assertCount(1, $footerRows[0]);
        self::assertInstanceOf(FooterDefinition::class, $footerRows[0][0]);
        self::assertSame('Total', $footerRows[0][0]->getValue());
    }

    public function testLoadWithoutColumnsThrows(): void
    {
        $this->expectException(GridWithoutColumnException::class);

        $this->loader->load('myGrid', []);
    }

    public function testLoadWithEmptyColumnsThrows(): void
    {
        $this->expectException(GridWithoutColumnException::class);

        $this->loader->load('myGrid', ['columns' => []]);
    }

    public function testLoadReturnsEmptyRowConfigWhenNoRowsKey(): void
    {
        $config = [
            'columns' => [
                ['source' => 'item.name'],
            ],
        ];

        $result = $this->loader->load('myGrid', $config);

        self::assertNull($result->getRowDefinition()->getLink());
        self::assertSame([], $result->getRowDefinition()->getVariables()->all());
    }

    public function testLoadReturnsEmptyFooterWhenNoFooterKey(): void
    {
        $config = [
            'columns' => [
                ['source' => 'item.name'],
            ],
        ];

        $result = $this->loader->load('myGrid', $config);

        self::assertSame([], iterator_to_array($result->getFooterDefinitions()));
    }
}
