<?php

namespace Jmf\Grid\RenderingPreset;

interface WithRenderingPresetInterface
{
    public function getAlign(): ?string;

    public function getLabel(): ?string;

    public function getSource(): ?string;

    public function getTemplate(): ?string;

    public function getPreset(): ?string;

    public function applyPreset(RenderingPreset $renderingPreset): static;
}
