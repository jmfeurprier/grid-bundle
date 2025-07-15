<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration;

use Jmf\Grid\Exception\GridException;
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
     *
     * @throws GridException
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
            $this->getPresetId(),
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

    /**
     * @return null|non-empty-string
     */
    private function getSource(): ?string
    {
        $source = $this->columnConfig['source'] ?? null;

        Assert::nullOrStringNotEmpty($source);

        return $source;
    }

    private function getTemplate(): ?string
    {
        $template = $this->columnConfig['template'] ?? null;

        Assert::nullOrString($template);

        return $template;
    }

    /**
     * @return null|non-empty-string
     */
    private function getPresetId(): ?string
    {
        $presetId = $this->columnConfig['preset'] ?? null;

        Assert::nullOrStringNotEmpty($presetId);

        return $presetId;
    }
}
