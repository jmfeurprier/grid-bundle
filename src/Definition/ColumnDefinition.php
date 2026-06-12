<?php

declare(strict_types=1);

namespace Jmf\Grid\Definition;

use Jmf\RenderingPreset\Preset\Preset;
use Jmf\TemplateRendering\TemplateInterface;
use Override;
use Webmozart\Assert\Assert;

readonly class ColumnDefinition implements WithPresetInterface
{
    /**
     * @param null|non-empty-string $align
     * @param null|non-empty-string $source
     * @param null|non-empty-string $presetId
     */
    final public function __construct(
        private ?string $align,
        private ?string $label,
        private ?string $source,
        private ?TemplateInterface $template,
        private ?string $presetId = null,
    ) {
    }

    /**
     * @return null|non-empty-string
     */
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
        $presetAlign = $preset->getProperties()->tryGetValue('align');
        $presetLabel = $preset->getProperties()->tryGetValue('label');

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
