<?php

namespace Jmf\Grid\Configuration;

use Jmf\Grid\RenderingPreset\RenderingPreset;
use Jmf\Grid\RenderingPreset\WithRenderingPresetInterface;
use Override;

readonly class ColumnConfiguration implements WithRenderingPresetInterface
{
    final public function __construct(
        private ?string $align,
        private ?string $label,
        private ?string $source,
        private ?string $template,
        private ?string $preset,
    ) {
    }

    public function getAlign(): ?string
    {
        return $this->align;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function getPreset(): ?string
    {
        return $this->preset;
    }

    #[Override]
    public function applyPreset(RenderingPreset $renderingPreset): static
    {
        return new static(
            $renderingPreset->getAlign() ?? $this->align,
            $renderingPreset->getLabel() ?? $this->label,
            $renderingPreset->getSource() ?? $this->source,
            $renderingPreset->getTemplate() ?? $this->template,
            $renderingPreset->getPreset() ?? null,
        );
    }
}
