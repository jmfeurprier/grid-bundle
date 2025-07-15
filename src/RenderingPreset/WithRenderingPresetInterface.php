<?php

declare(strict_types=1);

namespace Jmf\Grid\RenderingPreset;

interface WithRenderingPresetInterface
{
    /**
     * @return null|non-empty-string
     */
    public function getPresetId(): ?string;

    public function applyPreset(RenderingPreset $renderingPreset): static;
}
