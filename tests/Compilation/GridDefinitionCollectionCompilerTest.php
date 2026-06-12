<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Compilation;

use Jmf\Grid\Compilation\AttributesCompiler;
use Jmf\Grid\Compilation\ColumnDefinitionCompiler;
use Jmf\Grid\Compilation\FooterDefinitionCompiler;
use Jmf\Grid\Compilation\GridDefinitionCollectionCompiler;
use Jmf\Grid\Compilation\GridDefinitionCompiler;
use Jmf\Grid\Compilation\RowDefinitionCompiler;
use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\Grid\Compilation\PresetApplier;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Override;
use PHPUnit\Framework\TestCase;

final class GridDefinitionCollectionCompilerTest extends TestCase
{
    private GridDefinitionCollectionCompiler $gridDefinitionCollectionCompiler;

    #[Override]
    protected function setUp(): void
    {
        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetApplier    = new PresetApplier($presetRepository);

        $gridDefinitionCompiler = new GridDefinitionCompiler(
            columnDefinitionCompiler: new ColumnDefinitionCompiler($presetApplier),
            rowDefinitionCompiler:    new RowDefinitionCompiler(
                                          new AttributesCompiler(),
                                      ),
            footerDefinitionCompiler: new FooterDefinitionCompiler($presetApplier),
        );

        $this->gridDefinitionCollectionCompiler = new GridDefinitionCollectionCompiler(
            $gridDefinitionCompiler,
        );
    }

    public function testCompileEmptyConfigReturnsEmptyCollection(): void
    {
        $result = $this->gridDefinitionCollectionCompiler->compile([]);

        self::assertSame([], $result->all());
    }

    public function testCompileSingleGrid(): void
    {
        $gridConfigs = [
            'myGrid' => [
                'columns' => [
                    ['source' => 'item.name'],
                ],
            ],
        ];

        $result = $this->gridDefinitionCollectionCompiler->compile($gridConfigs);

        self::assertCount(1, $result->all());

        $gridDefinition = $result->get('myGrid');

        self::assertSame('myGrid', $gridDefinition->getId());
        self::assertCount(1, $gridDefinition->getColumnDefinitions());
    }

    public function testCompileMultipleGrids(): void
    {
        $gridConfigs = [
            'gridA' => [
                'columns' => [['source' => 'item.name']],
            ],
            'gridB' => [
                'columns' => [['source' => 'item.title']],
            ],
        ];

        $collection = $this->gridDefinitionCollectionCompiler->compile($gridConfigs);

        $all = $collection->all();
        self::assertCount(2, $all);

        self::assertSame('gridA', $collection->get('gridA')->getId());
        self::assertSame('gridB', $collection->get('gridB')->getId());
    }

    public function testCompileedGridHasCorrectColumns(): void
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

        $collection = $this->gridDefinitionCollectionCompiler->compile($gridConfigs);
        $grid       = $collection->get('myGrid');

        $columns = iterator_to_array($grid->getColumnDefinitions());
        self::assertCount(1, $columns);
        self::assertInstanceOf(ColumnDefinition::class, $columns[0]);
        self::assertSame('item.name', $columns[0]->getSource());
        self::assertSame('Name', $columns[0]->getLabel());
    }
}
