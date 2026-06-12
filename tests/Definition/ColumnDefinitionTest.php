<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Model\Column;

use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\Property\PresetProperty;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\TemplateRendering\StringTemplate;
use PHPUnit\Framework\TestCase;

final class ColumnDefinitionTest extends TestCase
{
    public function testApplyPresetWithEmptyPreset(): void
    {
        $columnDefinition = new ColumnDefinition(
            align:    'abc',
            label:    'def',
            source:   'ghi',
            template: new StringTemplate('jkl'),
            presetId: null,
        );

        $preset = new Preset(
            id:         'foo',
            source:     null,
            template:   null,
            properties: new PresetPropertyCollection([]),
        );

        $result = $columnDefinition->applyPreset($preset);

        self::assertSame('abc', $result->getAlign());
        self::assertSame('def', $result->getLabel());
        self::assertSame('ghi', $result->getSource());
        self::assertEquals(new StringTemplate('jkl'), $result->getTemplate());
        self::assertNull($result->getPresetId());
    }

    public function testApplyPresetWithEmptyColumnDefinitionAndPopulatedPreset(): void
    {
        $columnDefinition = new ColumnDefinition(
            align:    null,
            label:    null,
            source:   null,
            template: null,
            presetId: null,
        );

        $preset = new Preset(
            id:         'foo',
            source:     'ghi',
            template:   new StringTemplate('jkl'),
            properties: new PresetPropertyCollection(
                            [
                                new PresetProperty('align', 'abc'),
                                new PresetProperty('label', 'def'),
                            ],
                        ),
        );

        $result = $columnDefinition->applyPreset($preset);

        self::assertSame('abc', $result->getAlign());
        self::assertSame('def', $result->getLabel());
        self::assertSame('ghi', $result->getSource());
        self::assertEquals(new StringTemplate('jkl'), $result->getTemplate());
        self::assertNull($result->getPresetId());
    }

    public function testApplyPresetWithPopulatedColumnDefinitionAndPopulatedPreset(): void
    {
        $columnDefinition = new ColumnDefinition(
            align:    'abc',
            label:    'def',
            source:   'ghi',
            template: new StringTemplate('jkl'),
            presetId: null,
        );

        $preset = new Preset(
            id:         'foo',
            source:     'mno',
            template:   new StringTemplate('pqr'),
            properties: new PresetPropertyCollection(
                            [
                                new PresetProperty('align', 'stu'),
                                new PresetProperty('label', 'vwx'),
                            ],
                        ),
        );

        $result = $columnDefinition->applyPreset($preset);

        self::assertSame('abc', $result->getAlign());
        self::assertSame('def', $result->getLabel());
        self::assertSame('ghi', $result->getSource());
        self::assertEquals(new StringTemplate('jkl'), $result->getTemplate());
        self::assertNull($result->getPresetId());
    }
}
