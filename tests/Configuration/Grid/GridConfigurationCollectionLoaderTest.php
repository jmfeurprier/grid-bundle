<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Grid;

use Jmf\Grid\Configuration\Attributes\AttributesLoader;
use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Column\ColumnConfigurationLoader;
use Jmf\Grid\Configuration\Footer\FooterConfigurationLoader;
use Jmf\Grid\Configuration\Grid\GridConfigurationCollectionLoader;
use Jmf\Grid\Configuration\Grid\GridConfigurationLoader;
use Jmf\Grid\Configuration\Preset\PresetApplier;
use Jmf\Grid\Configuration\Row\RowConfigurationLoader;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Override;
use PHPUnit\Framework\TestCase;

final class GridConfigurationCollectionLoaderTest extends TestCase
{
    private GridConfigurationCollectionLoader $loader;

    #[Override]
    protected function setUp(): void
    {
        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetApplier    = new PresetApplier($presetRepository);

        $gridConfigurationLoader = new GridConfigurationLoader(
            columnConfigurationLoader: new ColumnConfigurationLoader($presetApplier),
            rowConfigurationLoader:    new RowConfigurationLoader(
                                           new AttributesLoader(),
                                       ),
            footerConfigurationLoader: new FooterConfigurationLoader($presetApplier),
        );

        $this->loader = new GridConfigurationCollectionLoader($gridConfigurationLoader);
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

        $gridConfiguration = $result->get('myGrid');

        self::assertSame('myGrid', $gridConfiguration->getId());
        self::assertCount(1, $gridConfiguration->getColumnConfigurations());
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

        $columns = iterator_to_array($grid->getColumnConfigurations());
        self::assertCount(1, $columns);
        self::assertInstanceOf(ColumnConfiguration::class, $columns[0]);
        self::assertSame('item.name', $columns[0]->getSource());
        self::assertSame('Name', $columns[0]->getLabel());
    }
}
