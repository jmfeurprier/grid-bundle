<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Compilation;

use Jmf\Grid\Compilation\FooterDefinitionCompiler;
use Jmf\Grid\Compilation\PresetApplier;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Jmf\TemplateRendering\StringTemplate;
use Override;
use PHPUnit\Framework\TestCase;

final class FooterDefinitionCompilerTest extends TestCase
{
    private FooterDefinitionCompiler $footerDefinitionCompiler;

    #[Override]
    protected function setUp(): void
    {
        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetApplier    = new PresetApplier($presetRepository);

        $this->footerDefinitionCompiler = new FooterDefinitionCompiler($presetApplier);
    }

    public function testCompileMinimalConfig(): void
    {
        $result = $this->footerDefinitionCompiler->compile(
            [
                'value' => 'Total',
            ],
        );

        self::assertSame('Total', $result->getValue());
        self::assertNull($result->getAlign());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getMerge());
        self::assertNull($result->getPresetId());
    }

    public function testCompileFullConfig(): void
    {
        $result = $this->footerDefinitionCompiler->compile(
            [
                'align' => 'right',
                'merge' => 2,
                'value' => 'Total',
            ],
        );

        self::assertSame('right', $result->getAlign());
        self::assertSame(2, $result->getMerge());
        self::assertSame('Total', $result->getValue());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getPresetId());
    }

    public function testCompileWithTemplate(): void
    {
        $result = $this->footerDefinitionCompiler->compile(
            [
                'template' => '<b>{{ value }}</b>',
            ],
        );

        self::assertEquals(new StringTemplate('<b>{{ value }}</b>'), $result->getTemplate());
    }

    public function testCompileWithEmptyConfig(): void
    {
        $result = $this->footerDefinitionCompiler->compile([]);

        self::assertNull($result->getAlign());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getMerge());
        self::assertNull($result->getValue());
        self::assertNull($result->getPresetId());
    }
}
