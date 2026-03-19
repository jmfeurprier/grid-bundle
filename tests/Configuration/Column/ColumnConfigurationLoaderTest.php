<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Column;

use Override;
use Jmf\Grid\Configuration\Column\ColumnConfigurationLoader;
use Jmf\Grid\Configuration\Preset\PresetApplier;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Jmf\TemplateRendering\StringTemplate;
use PHPUnit\Framework\TestCase;

final class ColumnConfigurationLoaderTest extends TestCase
{
    private ColumnConfigurationLoader $loader;

    #[Override]
    protected function setUp(): void
    {
        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetApplier    = new PresetApplier($presetRepository);

        $this->loader = new ColumnConfigurationLoader($presetApplier);
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
