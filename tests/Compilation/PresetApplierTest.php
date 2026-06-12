<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Compilation;

use Jmf\Grid\Compilation\PresetApplier;
use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\Grid\Definition\WithPresetInterface;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PresetApplierTest extends TestCase
{
    private PresetRepositoryInterface&MockObject $presetRepository;

    private PresetApplier $presetApplier;

    #[Override]
    protected function setUp(): void
    {
        $this->presetRepository = $this->createMock(PresetRepositoryInterface::class);

        $this->presetApplier = new PresetApplier($this->presetRepository);
    }

    public function testApplyWithNoPresetIdReturnsSubjectUnchanged(): void
    {
        $subject = $this->createMock(WithPresetInterface::class);
        $subject->method('getPresetId')->willReturn(null);
        $subject->expects($this->never())->method('applyPreset');

        $this->presetRepository->expects($this->never())->method('get');

        $result = $this->presetApplier->apply($subject);

        self::assertSame($subject, $result);
    }

    public function testApplyWithPresetIdFetchesPresetAndAppliesIt(): void
    {
        $preset = new Preset('myPreset', 'preset.source', null, new PresetPropertyCollection([]));

        $this->presetRepository->expects(self::once())->method('get')->with('myPreset')->willReturn($preset);

        $columnDefinition = new ColumnDefinition(
            align:    null,
            label:    null,
            source:   null,
            template: null,
            presetId: 'myPreset',
        );

        $result = $this->presetApplier->apply($columnDefinition);

        self::assertNotSame($columnDefinition, $result);
        self::assertSame('preset.source', $result->getSource());
        self::assertNull($result->getPresetId());
    }

    public function testApplyPreservesOwnValuesOverPresetValues(): void
    {
        $preset = new Preset('myPreset', 'preset.source', null, new PresetPropertyCollection([]));

        $this->presetRepository->expects(self::once())->method('get')->with('myPreset')->willReturn($preset);

        $columnDefinition = new ColumnDefinition(
            align:    null,
            label:    null,
            source:   'own.source',
            template: null,
            presetId: 'myPreset',
        );

        $result = $this->presetApplier->apply($columnDefinition);

        self::assertSame('own.source', $result->getSource());
    }
}
