<?php

namespace Jmf\Grid\Configuration;

use Jmf\Grid\RenderingPreset\RenderingPresetApplier;
use Webmozart\Assert\Assert;

class FooterConfigurationLoader
{
    /**
     * @var array<string, mixed>
     */
    private array $footerConfig;

    public function __construct(
        private readonly RenderingPresetApplier $renderingPresetApplier,
    ) {
    }

    /**
     * @param array<string, mixed> $footerConfig
     */
    public function load(array $footerConfig): FooterConfiguration
    {
        Assert::isMap($footerConfig);

        $this->footerConfig = $footerConfig;

        $footerConfiguration = new FooterConfiguration(
            $this->getAlign(),
            $this->getTemplate(),
            $this->getMerge(),
            $this->getValue(),
            $this->getPreset(),
        );

        return $this->renderingPresetApplier->apply($footerConfiguration);
    }

    private function getAlign(): ?string
    {
        $align = $this->footerConfig['align'] ?? null;

        Assert::nullOrString($align);

        return $align;
    }

    private function getTemplate(): ?string
    {
        $template = $this->footerConfig['template'] ?? null;

        Assert::nullOrString($template);

        return $template;
    }

    private function getMerge(): ?int
    {
        $merge = $this->footerConfig['merge'] ?? null;

        Assert::nullOrPositiveInteger($merge);

        return $merge;
    }

    private function getValue(): ?string
    {
        $value = $this->footerConfig['value'] ?? null;

        Assert::nullOrString($value);

        return $value;
    }

    private function getPreset(): ?string
    {
        $preset = $this->footerConfig['preset'] ?? null;

        Assert::nullOrString($preset);

        return $preset;
    }
}
