<?php

namespace Jmf\Grid\Configuration;

use Jmf\Grid\RenderingPreset\RenderingPreset;
use Jmf\Grid\RenderingPreset\WithRenderingPresetInterface;
use Override;

readonly class FooterConfiguration implements WithRenderingPresetInterface
{
    final public function __construct(
        private ?string $align,
        private ?string $template,
        private ?int $merge,
        private ?string $value,
        private ?string $preset,
    ) {
    }

    #[Override]
    public function getAlign(): ?string
    {
        return $this->align;
    }

    #[Override]
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    public function getMerge(): ?int
    {
        return $this->merge;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    #[Override]
    public function getLabel(): ?string
    {
        return null;
    }

    #[Override]
    public function getSource(): ?string
    {
        return null;
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
            $this->getAlign() ?? $renderingPreset->getAlign(),
            $this->getTemplate() ?? $renderingPreset->getTemplate(),
            $this->getMerge(),
            $this->getValue(),
            $renderingPreset->getPreset(),
        );
    }
}
