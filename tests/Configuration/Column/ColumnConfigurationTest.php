<?php

namespace Jmf\Grid\Tests\Configuration\Column;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\Property\PresetProperty;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\TemplateRendering\StringTemplate;
use PHPUnit\Framework\TestCase;

class ColumnConfigurationTest extends TestCase
{
    public function testApplyPresetWithEmptyPreset()
    {
        $columnConfiguration = new ColumnConfiguration(
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

        $result = $columnConfiguration->applyPreset($preset);

        self::assertSame('abc', $result->getAlign());
        self::assertSame('def', $result->getLabel());
        self::assertSame('ghi', $result->getSource());
        self::assertEquals(new StringTemplate('jkl'), $result->getTemplate());
        self::assertNull($result->getPresetId());
    }

    public function testApplyPresetWithEmptyColumnConfigurationAndPopulatedPreset()
    {
        $columnConfiguration = new ColumnConfiguration(
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

        $result = $columnConfiguration->applyPreset($preset);

        self::assertSame('abc', $result->getAlign());
        self::assertSame('def', $result->getLabel());
        self::assertSame('ghi', $result->getSource());
        self::assertEquals(new StringTemplate('jkl'), $result->getTemplate());
        self::assertNull($result->getPresetId());
    }

    public function testApplyPresetWithPopulatedColumnConfigurationAndPopulatedPreset()
    {
        $columnConfiguration = new ColumnConfiguration(
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

        $result = $columnConfiguration->applyPreset($preset);

        self::assertSame('abc', $result->getAlign());
        self::assertSame('def', $result->getLabel());
        self::assertSame('ghi', $result->getSource());
        self::assertEquals(new StringTemplate('jkl'), $result->getTemplate());
        self::assertNull($result->getPresetId());
    }
}
