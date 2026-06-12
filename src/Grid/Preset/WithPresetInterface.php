<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Preset;

use Jmf\RenderingPreset\Preset\Preset;

interface WithPresetInterface
{
    /**
     * @return null|non-empty-string
     */
    public function getPresetId(): ?string;

    public function applyPreset(Preset $preset): static;
}
