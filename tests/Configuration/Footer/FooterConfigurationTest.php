<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Footer;

use Jmf\Grid\Configuration\Footer\FooterConfiguration;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\Property\PresetProperty;
use Jmf\RenderingPreset\Preset\Property\PresetPropertyCollection;
use Jmf\TemplateRendering\StringTemplate;
use PHPUnit\Framework\TestCase;

final class FooterConfigurationTest extends TestCase
{
    public function testGetters(): void
    {
        $template = new StringTemplate('<b>{{ value }}</b>');

        $footerConfiguration = new FooterConfiguration(
            align:    'center',
            template: $template,
            merge:    3,
            value:    'Total',
            presetId: 'myPreset',
        );

        self::assertSame('center', $footerConfiguration->getAlign());
        self::assertSame($template, $footerConfiguration->getTemplate());
        self::assertSame(3, $footerConfiguration->getMerge());
        self::assertSame('Total', $footerConfiguration->getValue());
        self::assertSame('myPreset', $footerConfiguration->getPresetId());
        self::assertNull($footerConfiguration->getLabel());
        self::assertNull($footerConfiguration->getSource());
    }

    public function testGettersWithNullValues(): void
    {
        $footerConfiguration = new FooterConfiguration(
            align:    null,
            template: null,
            merge:    null,
            value:    null,
            presetId: null,
        );

        self::assertNull($footerConfiguration->getAlign());
        self::assertNull($footerConfiguration->getTemplate());
        self::assertNull($footerConfiguration->getMerge());
        self::assertNull($footerConfiguration->getValue());
        self::assertNull($footerConfiguration->getPresetId());
    }

    public function testApplyPresetWithEmptyPreset(): void
    {
        $template = new StringTemplate('<b>{{ value }}</b>');

        $footerConfiguration = new FooterConfiguration(
            align:    'center',
            template: $template,
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

        $result = $footerConfiguration->applyPreset($preset);

        self::assertSame('center', $result->getAlign());
        self::assertSame($template, $result->getTemplate());
        self::assertSame(2, $result->getMerge());
        self::assertSame('Total', $result->getValue());
        self::assertSame('myPreset', $result->getPresetId());
    }

    public function testApplyPresetFillsMissingValuesFromPreset(): void
    {
        $presetTemplate = new StringTemplate('<i>{{ value }}</i>');

        $footerConfiguration = new FooterConfiguration(
            align:    null,
            template: null,
            merge:    null,
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
                                new PresetProperty('merge', 3),
                            ],
                        ),
        );

        $result = $footerConfiguration->applyPreset($preset);

        self::assertSame('right', $result->getAlign());
        self::assertSame($presetTemplate, $result->getTemplate());
        self::assertSame(3, $result->getMerge());
        self::assertSame('Total', $result->getValue());
        self::assertSame('myPreset', $result->getPresetId());
    }

    public function testApplyPresetDoesNotOverrideExistingValues(): void
    {
        $ownTemplate    = new StringTemplate('<b>{{ value }}</b>');
        $presetTemplate = new StringTemplate('<i>{{ value }}</i>');

        $footerConfiguration = new FooterConfiguration(
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

        $result = $footerConfiguration->applyPreset($preset);

        self::assertSame('center', $result->getAlign());
        self::assertSame($ownTemplate, $result->getTemplate());
        self::assertSame(2, $result->getMerge());
    }
}
