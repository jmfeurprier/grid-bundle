<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Preset;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Preset\PresetApplier;
use Jmf\Grid\Configuration\Preset\WithPresetInterface;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\PresetCollection;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use PHPUnit\Framework\TestCase;

final class PresetApplierTest extends TestCase
{
    public function testApplyWithNoPresetIdReturnsSubjectUnchanged(): void
    {
        $subject = $this->createMock(WithPresetInterface::class);
        $subject->method('getPresetId')->willReturn(null);
        $subject->expects($this->never())->method('applyPreset');

        $presetRepository = $this->createMock(PresetRepositoryInterface::class);
        $presetRepository->expects($this->never())->method('getCollection');

        $applier = new PresetApplier($presetRepository);
        $result  = $applier->apply($subject);

        self::assertSame($subject, $result);
    }

    public function testApplyWithPresetIdFetchesPresetAndAppliesIt(): void
    {
        $preset     = new Preset('myPreset', 'preset.source', null, new PresetPropertyCollection([]));
        $collection = new PresetCollection([$preset]);

        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetRepository->method('getCollection')->willReturn($collection);

        $subject = new ColumnConfiguration(
            align:    null,
            label:    null,
            source:   null,
            template: null,
            presetId: 'myPreset',
        );

        $applier = new PresetApplier($presetRepository);
        $result  = $applier->apply($subject);

        self::assertNotSame($subject, $result);
        self::assertSame('preset.source', $result->getSource());
        self::assertNull($result->getPresetId());
    }

    public function testApplyPreservesOwnValuesOverPresetValues(): void
    {
        $preset     = new Preset('myPreset', 'preset.source', null, new PresetPropertyCollection([]));
        $collection = new PresetCollection([$preset]);

        $presetRepository = $this->createStub(PresetRepositoryInterface::class);
        $presetRepository->method('getCollection')->willReturn($collection);

        $subject = new ColumnConfiguration(
            align:    null,
            label:    null,
            source:   'own.source',
            template: null,
            presetId: 'myPreset',
        );

        $applier = new PresetApplier($presetRepository);
        $result  = $applier->apply($subject);

        self::assertSame('own.source', $result->getSource());
    }
}
