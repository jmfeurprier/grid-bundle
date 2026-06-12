<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Grid;

use Jmf\Grid\Grid\Row\AttributesLoader;
use Jmf\Grid\Grid\Column\ColumnDefinition;
use Jmf\Grid\Grid\Column\ColumnDefinitionLoader;
use Jmf\Grid\Grid\Footer\FooterDefinitionLoader;
use Jmf\Grid\Grid\GridDefinitionCollectionLoader;
use Jmf\Grid\Grid\GridDefinitionLoader;
use Jmf\Grid\Grid\Preset\PresetApplier;
use Jmf\Grid\Grid\Row\RowDefinitionLoader;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Override;
use PHPUnit\Framework\TestCase;

final class GridDefinitionCollectionLoaderTest extends TestCase
{
    private GridDefinitionCollectionLoader $loader;

    #[Override]
    protected function setUp(): void
    {
        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetApplier    = new PresetApplier($presetRepository);

        $gridDefinitionLoader = new GridDefinitionLoader(
            columnDefinitionLoader: new ColumnDefinitionLoader($presetApplier),
            rowDefinitionLoader:    new RowDefinitionLoader(
                                           new AttributesLoader(),
                                       ),
            footerDefinitionLoader: new FooterDefinitionLoader($presetApplier),
        );

        $this->loader = new GridDefinitionCollectionLoader($gridDefinitionLoader);
    }

    public function testLoadEmptyConfigReturnsEmptyCollection(): void
    {
        $result = $this->loader->load([]);

        self::assertSame([], $result->all());
    }

    public function testLoadSingleGrid(): void
    {
        $gridConfigs = [
            'myGrid' => [
                'columns' => [
                    ['source' => 'item.name'],
                ],
            ],
        ];

        $result = $this->loader->load($gridConfigs);

        self::assertCount(1, $result->all());

        $gridDefinition = $result->get('myGrid');

        self::assertSame('myGrid', $gridDefinition->getId());
        self::assertCount(1, $gridDefinition->getColumnDefinitions());
    }

    public function testLoadMultipleGrids(): void
    {
        $gridConfigs = [
            'gridA' => [
                'columns' => [['source' => 'item.name']],
            ],
            'gridB' => [
                'columns' => [['source' => 'item.title']],
            ],
        ];

        $collection = $this->loader->load($gridConfigs);

        $all = $collection->all();
        self::assertCount(2, $all);

        self::assertSame('gridA', $collection->get('gridA')->getId());
        self::assertSame('gridB', $collection->get('gridB')->getId());
    }

    public function testLoadedGridHasCorrectColumns(): void
    {
        $gridConfigs = [
            'myGrid' => [
                'columns' => [
                    [
                        'source' => 'item.name',
                        'label'  => 'Name',
                    ],
                ],
            ],
        ];

        $collection = $this->loader->load($gridConfigs);
        $grid       = $collection->get('myGrid');

        $columns = iterator_to_array($grid->getColumnDefinitions());
        self::assertCount(1, $columns);
        self::assertInstanceOf(ColumnDefinition::class, $columns[0]);
        self::assertSame('item.name', $columns[0]->getSource());
        self::assertSame('Name', $columns[0]->getLabel());
    }
}
