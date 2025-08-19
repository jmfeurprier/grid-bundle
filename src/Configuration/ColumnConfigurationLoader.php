<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration;

use Jmf\Grid\Preset\PresetApplier;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\TemplateRendering\StringTemplate;
use Jmf\TemplateRendering\TemplateInterface;
use Webmozart\Assert\Assert;

class ColumnConfigurationLoader
{
    /**
     * @var array<string, mixed>
     */
    private array $columnConfig;

    public function __construct(
        private readonly PresetApplier $presetApplier,
    ) {
    }

    /**
     * @param array<string, mixed> $columnConfig
     *
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
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

        return $this->presetApplier->apply($columnConfiguration);
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

    private function getTemplate(): ?TemplateInterface
    {
        $template = $this->columnConfig['template'] ?? null;

        if (null === $template) {
            return null;
        }

        Assert::string($template);

        // @todo
        return new StringTemplate($template);
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
