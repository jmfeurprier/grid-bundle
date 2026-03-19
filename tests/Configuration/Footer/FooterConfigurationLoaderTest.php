<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Footer;

use Override;
use Jmf\Grid\Configuration\Footer\FooterConfigurationLoader;
use Jmf\Grid\Configuration\Preset\PresetApplier;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Jmf\TemplateRendering\StringTemplate;
use PHPUnit\Framework\TestCase;

final class FooterConfigurationLoaderTest extends TestCase
{
    private FooterConfigurationLoader $loader;

    #[Override]
    protected function setUp(): void
    {
        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetApplier    = new PresetApplier($presetRepository);

        $this->loader = new FooterConfigurationLoader($presetApplier);
    }

    public function testLoadMinimalConfig(): void
    {
        $result = $this->loader->load(['value' => 'Total']);

        self::assertSame('Total', $result->getValue());
        self::assertNull($result->getAlign());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getMerge());
        self::assertNull($result->getPresetId());
    }

    public function testLoadFullConfig(): void
    {
        $result = $this->loader->load([
                                          'align' => 'right',
                                          'merge' => 2,
                                          'value' => 'Total',
                                      ]);

        self::assertSame('right', $result->getAlign());
        self::assertSame(2, $result->getMerge());
        self::assertSame('Total', $result->getValue());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getPresetId());
    }

    public function testLoadWithTemplate(): void
    {
        $result = $this->loader->load([
                                          'template' => '<b>{{ value }}</b>',
                                      ]);

        self::assertEquals(new StringTemplate('<b>{{ value }}</b>'), $result->getTemplate());
    }

    public function testLoadWithEmptyConfig(): void
    {
        $result = $this->loader->load([]);

        self::assertNull($result->getAlign());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getMerge());
        self::assertNull($result->getValue());
        self::assertNull($result->getPresetId());
    }
}
