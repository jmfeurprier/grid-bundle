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
use Override;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
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

        $this->presetRepository->expects($this->never())->method('getCollection');

        $result = $this->presetApplier->apply($subject);

        self::assertSame($subject, $result);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testApplyWithPresetIdFetchesPresetAndAppliesIt(): void
    {
        $preset           = new Preset('myPreset', 'preset.source', null, new PresetPropertyCollection([]));
        $presetCollection = new PresetCollection([$preset]);

        $this->presetRepository->method('getCollection')->willReturn($presetCollection);

        $columnConfiguration = new ColumnConfiguration(
            align:    null,
            label:    null,
            source:   null,
            template: null,
            presetId: 'myPreset',
        );

        $result = $this->presetApplier->apply($columnConfiguration);

        self::assertNotSame($columnConfiguration, $result);
        self::assertSame('preset.source', $result->getSource());
        self::assertNull($result->getPresetId());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testApplyPreservesOwnValuesOverPresetValues(): void
    {
        $preset           = new Preset('myPreset', 'preset.source', null, new PresetPropertyCollection([]));
        $presetCollection = new PresetCollection([$preset]);

        $this->presetRepository->method('getCollection')->willReturn($presetCollection);

        $columnConfiguration = new ColumnConfiguration(
            align:    null,
            label:    null,
            source:   'own.source',
            template: null,
            presetId: 'myPreset',
        );

        $result = $this->presetApplier->apply($columnConfiguration);

        self::assertSame('own.source', $result->getSource());
    }
}
