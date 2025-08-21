<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Column;

use Jmf\Grid\Preset\WithPresetInterface;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\TemplateRendering\TemplateInterface;
use Override;
use Webmozart\Assert\Assert;

readonly class ColumnConfiguration implements WithPresetInterface
{
    /**
     * @param null|non-empty-string $presetId
     * @param null|non-empty-string $source
     */
    final public function __construct(
        private ?string $align,
        private ?string $label,
        private ?string $source,
        private ?TemplateInterface $template,
        private ?string $presetId = null,
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

    /**
     * @return null|non-empty-string
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getTemplate(): ?TemplateInterface
    {
        return $this->template;
    }

    #[Override]
    public function getPresetId(): ?string
    {
        return $this->presetId;
    }

    #[Override]
    public function applyPreset(Preset $preset): static
    {
        $properties  = $preset->getProperties();
        $presetAlign = $properties->tryGetValue('align');
        $presetLabel = $properties->tryGetValue('label');

        Assert::nullOrStringNotEmpty($presetAlign);
        Assert::nullOrString($presetLabel);

        return new static(
            $this->align ?? $presetAlign,
            $this->label ?? $presetLabel,
            $this->source ?? $preset->getSource(),
            $this->template ?? $preset->getTemplate(),
        );
    }
}
