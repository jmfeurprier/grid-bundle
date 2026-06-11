<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Grid;

use Jmf\Grid\Configuration\Attributes\AttributesLoader;
use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Column\ColumnConfigurationLoader;
use Jmf\Grid\Configuration\Footer\FooterConfiguration;
use Jmf\Grid\Configuration\Footer\FooterConfigurationLoader;
use Jmf\Grid\Configuration\Grid\GridConfigurationLoader;
use Jmf\Grid\Configuration\Preset\PresetApplier;
use Jmf\Grid\Configuration\Row\RowConfigurationLoader;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Override;
use PHPUnit\Framework\TestCase;

final class GridConfigurationLoaderTest extends TestCase
{
    private GridConfigurationLoader $loader;

    #[Override]
    protected function setUp(): void
    {
        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetApplier    = new PresetApplier($presetRepository);

        $this->loader = new GridConfigurationLoader(
            columnConfigurationLoader: new ColumnConfigurationLoader($presetApplier),
            rowConfigurationLoader:    new RowConfigurationLoader(
                                           new AttributesLoader(),
                                       ),
            footerConfigurationLoader: new FooterConfigurationLoader($presetApplier),
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

        $columns = iterator_to_array($result->getColumnConfigurations());

        self::assertCount(1, $columns);

        $column = $columns[0];

        self::assertInstanceOf(ColumnConfiguration::class, $column);
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

        self::assertSame('my_route', $result->getRowConfiguration()->getLink());
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

        $footerRows = iterator_to_array($result->getFooterConfigurations());
        self::assertCount(1, $footerRows);
        self::assertCount(1, $footerRows[0]);
        self::assertInstanceOf(FooterConfiguration::class, $footerRows[0][0]);
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

        self::assertNull($result->getRowConfiguration()->getLink());
        self::assertSame([], $result->getRowConfiguration()->getVariables()->all());
    }

    public function testLoadReturnsEmptyFooterWhenNoFooterKey(): void
    {
        $config = [
            'columns' => [
                ['source' => 'item.name'],
            ],
        ];

        $result = $this->loader->load('myGrid', $config);

        self::assertSame([], iterator_to_array($result->getFooterConfigurations()));
    }
}
