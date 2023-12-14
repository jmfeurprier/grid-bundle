<?php

namespace Jmf\Grid\RenderingPreset;

use Webmozart\Assert\Assert;

readonly class RenderingPresetCollectionLoader
{
    public function __construct(
        private RenderingPresetLoader $renderingPresetLoader,
    ) {
    }

    /**
     * @param array<string, mixed> $presetConfigs
     */
    public function load(array $presetConfigs): RenderingPresetCollection
    {
        Assert::isMap($presetConfigs);

        $renderingPresets = [];

        foreach ($presetConfigs as $presetId => $presetConfig) {
            Assert::isArray($presetConfig);

            $renderingPresets[$presetId] = $this->renderingPresetLoader->load($presetConfig);
        }

        return new RenderingPresetCollection($renderingPresets);
    }
}
