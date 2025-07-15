<?php

declare(strict_types=1);

namespace Jmf\Grid\RenderingPreset;

use Webmozart\Assert\Assert;

readonly class RenderingPresetLoader
{
    /**
     * @param array<string, mixed> $presetConfig
     */
    public function load(array $presetConfig): RenderingPreset
    {
        Assert::isMap($presetConfig);

        return new RenderingPreset(
            $this->getAlign($presetConfig),
            $this->getLabel($presetConfig),
            $this->getSource($presetConfig),
            $this->getTemplate($presetConfig),
            $this->getPresetId($presetConfig),
        );
    }

    /**
     * @param array<string, mixed> $presetConfig
     */
    private function getAlign(array $presetConfig): ?string
    {
        $align = $presetConfig['align'] ?? null;

        Assert::nullOrString($align);

        return $align;
    }

    /**
     * @param array<string, mixed> $presetConfig
     */
    private function getLabel(array $presetConfig): ?string
    {
        $label = $presetConfig['label'] ?? null;

        Assert::nullOrString($label);

        return $label;
    }

    /**
     * @param array<string, mixed> $presetConfig
     */
    private function getSource(array $presetConfig): ?string
    {
        $source = $presetConfig['source'] ?? null;

        Assert::nullOrString($source);

        return $source;
    }

    /**
     * @param array<string, mixed> $presetConfig
     */
    private function getTemplate(array $presetConfig): ?string
    {
        $template = $presetConfig['template'] ?? null;

        Assert::nullOrString($template);

        return $template;
    }

    /**
     * @param array<string, mixed> $presetConfig
     *
     * @return null|non-empty-string
     */
    private function getPresetId(array $presetConfig): ?string
    {
        $presetId = $presetConfig['preset'] ?? null;

        Assert::nullOrStringNotEmpty($presetId);

        return $presetId;
    }
}
