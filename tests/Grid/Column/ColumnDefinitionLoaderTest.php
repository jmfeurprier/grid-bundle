<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Column;

use Jmf\Grid\Grid\Preset\PresetApplier;
use Jmf\Grid\Grid\Column\ColumnDefinitionLoader;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Jmf\TemplateRendering\StringTemplate;
use Override;
use PHPUnit\Framework\TestCase;

final class ColumnDefinitionLoaderTest extends TestCase
{
    private ColumnDefinitionLoader $loader;

    #[Override]
    protected function setUp(): void
    {
        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetApplier    = new PresetApplier($presetRepository);

        $this->loader = new ColumnDefinitionLoader($presetApplier);
    }

    public function testLoadMinimalConfig(): void
    {
        $result = $this->loader->load(['source' => 'item.name']);

        self::assertSame('item.name', $result->getSource());
        self::assertNull($result->getAlign());
        self::assertNull($result->getLabel());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getPresetId());
    }

    public function testLoadFullConfig(): void
    {
        $result = $this->loader->load([
                                          'align'  => 'right',
                                          'label'  => 'Name',
                                          'source' => 'item.name',
                                      ]);

        self::assertSame('right', $result->getAlign());
        self::assertSame('Name', $result->getLabel());
        self::assertSame('item.name', $result->getSource());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getPresetId());
    }

    public function testLoadWithTemplate(): void
    {
        $result = $this->loader->load([
                                          'source'   => 'item.name',
                                          'template' => '<b>{{ value }}</b>',
                                      ]);

        self::assertEquals(new StringTemplate('<b>{{ value }}</b>'), $result->getTemplate());
    }

    public function testLoadWithEmptyConfig(): void
    {
        $result = $this->loader->load([]);

        self::assertNull($result->getAlign());
        self::assertNull($result->getLabel());
        self::assertNull($result->getSource());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getPresetId());
    }
}
