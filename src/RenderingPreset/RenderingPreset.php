<?php

declare(strict_types=1);

namespace Jmf\Grid\RenderingPreset;

readonly class RenderingPreset
{
    /**
     * @param null|non-empty-string $presetId
     */
    public function __construct(
        private ?string $align,
        private ?string $label,
        private ?string $source,
        private ?string $template,
        private ?string $presetId,
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

    /**
     * @return null|non-empty-string
     */
    public function getPresetId(): ?string
    {
        return $this->presetId;
    }
}
