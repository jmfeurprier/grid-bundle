<?php

declare(strict_types=1);

namespace Jmf\Grid\RenderingPreset;

use Webmozart\Assert\Assert;

readonly class RenderingPresetCollectionLoader
{
    public function __construct(
        private RenderingPresetLoader $renderingPresetLoader,
    ) {
    }

    /**
     * @param array<non-empty-string, mixed> $presetConfigs
     */
    public function load(array $presetConfigs): RenderingPresetCollection
    {
        Assert::isMap($presetConfigs);

        $renderingPresets = [];

        foreach ($presetConfigs as $presetId => $presetConfig) {
            Assert::stringNotEmpty($presetId);
            Assert::isMap($presetConfig);

            $renderingPresets[$presetId] = $this->renderingPresetLoader->load($presetConfig);
        }

        return new RenderingPresetCollection($renderingPresets);
    }
}
