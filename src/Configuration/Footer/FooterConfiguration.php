<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Footer;

use Jmf\Grid\Configuration\Preset\WithPresetInterface;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\TemplateRendering\TemplateInterface;
use Override;
use Webmozart\Assert\Assert;

readonly class FooterConfiguration implements WithPresetInterface
{
    /**
     * @param null|non-empty-string $presetId
     */
    final public function __construct(
        private ?string $align,
        private ?TemplateInterface $template,
        private ?int $merge,
        private ?string $value,
        private ?string $presetId,
    ) {
    }

    public function getAlign(): ?string
    {
        return $this->align;
    }

    public function getTemplate(): ?TemplateInterface
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
    public function applyPreset(Preset $preset): static
    {
        $presetAlign = $preset->getProperties()->tryGetValue('align');
        $presetMerge = $preset->getProperties()->tryGetValue('merge');

        Assert::nullOrStringNotEmpty($presetAlign);
        Assert::nullOrInteger($presetMerge);

        return new static(
            $this->align ?? $presetAlign,
            $this->template ?? $preset->getTemplate(),
            $this->merge ?? $presetMerge,
            $this->value,
            $preset->getId(),
        );
    }
}
