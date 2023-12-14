<?php

namespace Jmf\Grid\Configuration;

use Jmf\Grid\RenderingPreset\RenderingPresetApplier;
use Webmozart\Assert\Assert;

class ColumnConfigurationLoader
{
    /**
     * @var array<string, mixed>
     */
    private array $columnConfig;

    public function __construct(
        private readonly RenderingPresetApplier $renderingPresetApplier,
    ) {
    }

    /**
     * @param array<string, mixed> $columnConfig
     */
    public function load(array $columnConfig): ColumnConfiguration
    {
        Assert::isMap($columnConfig);

        $this->columnConfig = $columnConfig;

        $columnConfiguration = new ColumnConfiguration(
            $this->getAlign(),
            $this->getLabel(),
            $this->getSource(),
            $this->getTemplate(),
            $this->getPreset(),
        );

        return $this->renderingPresetApplier->apply($columnConfiguration);
    }

    private function getAlign(): ?string
    {
        $align = $this->columnConfig['align'] ?? null;

        Assert::nullOrString($align);

        return $align;
    }

    private function getLabel(): ?string
    {
        $label = $this->columnConfig['label'] ?? null;

        Assert::nullOrString($label);

        return $label;
    }

    private function getSource(): ?string
    {
        $source = $this->columnConfig['source'] ?? null;

        Assert::nullOrString($source);

        return $source;
    }

    private function getTemplate(): ?string
    {
        $template = $this->columnConfig['template'] ?? null;

        Assert::nullOrString($template);

        return $template;
    }

    private function getPreset(): ?string
    {
        $preset = $this->columnConfig['preset'] ?? null;

        Assert::nullOrString($preset);

        return $preset;
    }
}
