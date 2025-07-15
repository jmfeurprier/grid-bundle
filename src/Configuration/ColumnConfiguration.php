<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration;

use Jmf\Grid\RenderingPreset\RenderingPreset;
use Jmf\Grid\RenderingPreset\WithRenderingPresetInterface;
use Override;

readonly class ColumnConfiguration implements WithRenderingPresetInterface
{
    /**
     * @param null|non-empty-string $presetId
     */
    final public function __construct(
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

    #[Override]
    public function getPresetId(): ?string
    {
        return $this->presetId;
    }

    #[Override]
    public function applyPreset(RenderingPreset $renderingPreset): static
    {
        return new static(
            $this->align ?? $renderingPreset->getAlign(),
            $this->label ?? $renderingPreset->getLabel(),
            $this->source ?? $renderingPreset->getSource(),
            $this->template ?? $renderingPreset->getTemplate(),
            $renderingPreset->getPresetId(),
        );
    }
}
