<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Grid\Footer;

use Jmf\Grid\Grid\Footer\FooterDefinition;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\Property\PresetProperty;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\TemplateRendering\StringTemplate;
use PHPUnit\Framework\TestCase;

final class FooterDefinitionTest extends TestCase
{
    public function testGetters(): void
    {
        $stringTemplate = new StringTemplate('<b>{{ value }}</b>');

        $footerDefinition = new FooterDefinition(
            align:    'center',
            template: $stringTemplate,
            merge:    3,
            value:    'Total',
            presetId: 'myPreset',
        );

        self::assertSame('center', $footerDefinition->getAlign());
        self::assertSame($stringTemplate, $footerDefinition->getTemplate());
        self::assertSame(3, $footerDefinition->getMerge());
        self::assertSame('Total', $footerDefinition->getValue());
        self::assertSame('myPreset', $footerDefinition->getPresetId());
        self::assertNull($footerDefinition->getLabel());
        self::assertNull($footerDefinition->getSource());
    }

    public function testGettersWithNullValues(): void
    {
        $footerDefinition = new FooterDefinition(
            align:    null,
            template: null,
            merge:    null,
            value:    null,
            presetId: null,
        );

        self::assertNull($footerDefinition->getAlign());
        self::assertNull($footerDefinition->getTemplate());
        self::assertNull($footerDefinition->getMerge());
        self::assertNull($footerDefinition->getValue());
        self::assertNull($footerDefinition->getPresetId());
    }

    public function testApplyPresetWithEmptyPreset(): void
    {
        $stringTemplate = new StringTemplate('<b>{{ value }}</b>');

        $footerDefinition = new FooterDefinition(
            align:    'center',
            template: $stringTemplate,
            merge:    2,
            value:    'Total',
            presetId: null,
        );

        $preset = new Preset(
            id:         'myPreset',
            source:     null,
            template:   null,
            properties: new PresetPropertyCollection([]),
        );

        $result = $footerDefinition->applyPreset($preset);

        self::assertSame('center', $result->getAlign());
        self::assertSame($stringTemplate, $result->getTemplate());
        self::assertSame(2, $result->getMerge());
        self::assertSame('Total', $result->getValue());
        self::assertSame('myPreset', $result->getPresetId());
    }

    public function testApplyPresetFillsMissingValuesFromPreset(): void
    {
        $stringTemplate = new StringTemplate('<i>{{ value }}</i>');

        $footerDefinition = new FooterDefinition(
            align:    null,
            template: null,
            merge:    null,
            value:    'Total',
            presetId: null,
        );

        $preset = new Preset(
            id:         'myPreset',
            source:     null,
            template:   $stringTemplate,
            properties: new PresetPropertyCollection(
                            [
                                new PresetProperty('align', 'right'),
                                new PresetProperty('merge', 3),
                            ],
                        ),
        );

        $result = $footerDefinition->applyPreset($preset);

        self::assertSame('right', $result->getAlign());
        self::assertSame($stringTemplate, $result->getTemplate());
        self::assertSame(3, $result->getMerge());
        self::assertSame('Total', $result->getValue());
        self::assertSame('myPreset', $result->getPresetId());
    }

    public function testApplyPresetDoesNotOverrideExistingValues(): void
    {
        $ownTemplate    = new StringTemplate('<b>{{ value }}</b>');
        $presetTemplate = new StringTemplate('<i>{{ value }}</i>');

        $footerDefinition = new FooterDefinition(
            align:    'center',
            template: $ownTemplate,
            merge:    2,
            value:    'Total',
            presetId: null,
        );

        $preset = new Preset(
            id:         'myPreset',
            source:     null,
            template:   $presetTemplate,
            properties: new PresetPropertyCollection(
                            [
                                new PresetProperty('align', 'right'),
                                new PresetProperty('merge', 5),
                            ],
                        ),
        );

        $result = $footerDefinition->applyPreset($preset);

        self::assertSame('center', $result->getAlign());
        self::assertSame($ownTemplate, $result->getTemplate());
        self::assertSame(2, $result->getMerge());
    }
}
