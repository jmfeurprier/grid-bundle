<?php

namespace Jmf\Grid\RenderingPreset;

use RuntimeException;
use Webmozart\Assert\Assert;

class RenderingPresetLoader
{
    /**
     * @var array<string, mixed>
     */
    private array $presetConfig;

    /**
     * @param array<string, mixed> $presetConfig
     */
    public function load(array $presetConfig): RenderingPreset
    {
        Assert::isMap($presetConfig);

        $this->presetConfig = $presetConfig;

        return new RenderingPreset(
            $this->getAlign(),
            $this->getLabel(),
            $this->getSource(),
            $this->getTemplate(),
            $this->getPreset(),
        );
    }

    private function getAlign(): ?string
    {
        $align = $this->presetConfig['align'] ?? null;

        Assert::nullOrString($align);

        return $align;
    }

    private function getLabel(): ?string
    {
        $label = $this->presetConfig['label'] ?? null;

        Assert::nullOrString($label);

        return $label;
    }

    private function getSource(): ?string
    {
        $source = $this->presetConfig['source'] ?? null;

        Assert::nullOrString($source);

        return $source;
    }

    private function getTemplate(): ?string
    {
        $template = $this->presetConfig['template'] ?? null;

        Assert::nullOrString($template);

        return $template;
    }

    private function getPreset(): ?string
    {
        $preset = $this->presetConfig['preset'] ?? null;

        Assert::nullOrString($preset);

        return $preset;
    }
}
