<?php

namespace Jmf\Grid\RenderingPreset;

readonly class RenderingPreset
{
    public function __construct(
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
}
