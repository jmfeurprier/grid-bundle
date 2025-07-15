<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration;

use Jmf\Grid\RenderingPreset\RenderingPreset;
use Jmf\Grid\RenderingPreset\WithRenderingPresetInterface;
use Override;

readonly class FooterConfiguration implements WithRenderingPresetInterface
{
    /**
     * @param null|non-empty-string $presetId
     */
    final public function __construct(
        private ?string $align,
        private ?string $template,
        private ?int $merge,
        private ?string $value,
        private ?string $presetId,
    ) {
    }

    public function getAlign(): ?string
    {
        return $this->align;
    }

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

    public function getLabel(): ?string
    {
        return null;
    }

    public function getSource(): ?string
    {
        return null;
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
            $this->getAlign() ?? $renderingPreset->getAlign(),
            $this->getTemplate() ?? $renderingPreset->getTemplate(),
            $this->getMerge(),
            $this->getValue(),
            $renderingPreset->getPresetId(),
        );
    }
}
