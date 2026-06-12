<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Compilation;

use Jmf\Grid\Compilation\ColumnDefinitionCompiler;
use Jmf\Grid\Compilation\PresetApplier;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Jmf\TemplateRendering\StringTemplate;
use Override;
use PHPUnit\Framework\TestCase;

final class ColumnDefinitionCompilerTest extends TestCase
{
    private ColumnDefinitionCompiler $columnDefinitionCompiler;

    #[Override]
    protected function setUp(): void
    {
        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetApplier    = new PresetApplier($presetRepository);

        $this->columnDefinitionCompiler = new ColumnDefinitionCompiler($presetApplier);
    }

    public function testCompileMinimalConfig(): void
    {
        $result = $this->columnDefinitionCompiler->compile(
            [
                'source' => 'item.name',
            ],
        );

        self::assertSame('item.name', $result->getSource());
        self::assertNull($result->getAlign());
        self::assertNull($result->getLabel());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getPresetId());
    }

    public function testCompileFullConfig(): void
    {
        $result = $this->columnDefinitionCompiler->compile(
            [
                'align'  => 'right',
                'label'  => 'Name',
                'source' => 'item.name',
            ],
        );

        self::assertSame('right', $result->getAlign());
        self::assertSame('Name', $result->getLabel());
        self::assertSame('item.name', $result->getSource());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getPresetId());
    }

    public function testCompileWithTemplate(): void
    {
        $result = $this->columnDefinitionCompiler->compile(
            [
                'source'   => 'item.name',
                'template' => '<b>{{ value }}</b>',
            ],
        );

        self::assertEquals(new StringTemplate('<b>{{ value }}</b>'), $result->getTemplate());
    }

    public function testCompileWithEmptyConfig(): void
    {
        $result = $this->columnDefinitionCompiler->compile([]);

        self::assertNull($result->getAlign());
        self::assertNull($result->getLabel());
        self::assertNull($result->getSource());
        self::assertNull($result->getTemplate());
        self::assertNull($result->getPresetId());
    }
}
