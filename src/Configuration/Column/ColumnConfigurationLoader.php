<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Column;

use Jmf\Grid\Preset\PresetApplier;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\TemplateRendering\StringTemplate;
use Jmf\TemplateRendering\TemplateInterface;
use Webmozart\Assert\Assert;

readonly class ColumnConfigurationLoader
{
    public function __construct(
        private PresetApplier $presetApplier,
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

        $columnConfiguration = new ColumnConfiguration(
            $this->getAlign($columnConfig),
            $this->getLabel($columnConfig),
            $this->getSource($columnConfig),
            $this->getTemplate($columnConfig),
            $this->getPresetId($columnConfig),
        );

        return $this->presetApplier->apply($columnConfiguration);
    }

    /**
     * @param array<string, mixed> $columnConfig
     */
    private function getAlign(array $columnConfig): ?string
    {
        $align = $columnConfig['align'] ?? null;

        Assert::nullOrString($align);

        return $align;
    }

    /**
     * @param array<string, mixed> $columnConfig
     */
    private function getLabel(array $columnConfig): ?string
    {
        $label = $columnConfig['label'] ?? null;

        Assert::nullOrString($label);

        return $label;
    }

    /**
     * @param array<string, mixed> $columnConfig
     *
     * @return null|non-empty-string
     */
    private function getSource(array $columnConfig): ?string
    {
        $source = $columnConfig['source'] ?? null;

        Assert::nullOrStringNotEmpty($source);

        return $source;
    }

    /**
     * @param array<string, mixed> $columnConfig
     */
    private function getTemplate(array $columnConfig): ?TemplateInterface
    {
        $template = $columnConfig['template'] ?? null;

        if (null === $template) {
            return null;
        }

        Assert::string($template);

        // @todo
        return new StringTemplate($template);
    }


    /**
     * @param array<string, mixed> $columnConfig
     *
     * @return null|non-empty-string
     */
    private function getPresetId(array $columnConfig): ?string
    {
        $presetId = $columnConfig['preset'] ?? null;

        Assert::nullOrStringNotEmpty($presetId);

        return $presetId;
    }
}
