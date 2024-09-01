<?php

namespace Jmf\Grid\RenderingPreset;

use Jmf\Grid\Exception\GridException;
use Webmozart\Assert\Assert;

readonly class RenderingPresetCollection
{
    /**
     * @param array<string, RenderingPreset> $renderingPresets
     */
    public function __construct(
        private array $renderingPresets
    ) {
        Assert::isMap($this->renderingPresets);
        Assert::allIsInstanceOf($this->renderingPresets, RenderingPreset::class);
    }

    public function get(string $id): RenderingPreset
    {
        return $this->renderingPresets[$id]
            ?? throw new GridException("Rendering preset '{$id}' is not defined.");
    }
}
