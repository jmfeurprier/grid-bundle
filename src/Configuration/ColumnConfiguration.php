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

    #[Override]
    public function getAlign(): ?string
    {
        return $this->align;
    }

    #[Override]
    public function getLabel(): ?string
    {
        return $this->label;
    }

    #[Override]
    public function getSource(): ?string
    {
        return $this->source;
    }

    #[Override]
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    #[Override]
    public function getPreset(): ?string
    {
        return $this->preset;
    }

    #[Override]
    public function applyPreset(RenderingPreset $renderingPreset): static
    {
        return new static(
            $this->align ?? $renderingPreset->getAlign(),
            $this->label ?? $renderingPreset->getLabel(),
            $this->source ?? $renderingPreset->getSource(),
            $this->template ?? $renderingPreset->getTemplate(),
            $renderingPreset->getPreset(),
        );
    }
}
