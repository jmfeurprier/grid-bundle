<?php

declare(strict_types=1);

namespace Jmf\Grid\Compilation;

use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\TemplateRendering\StringTemplate;
use Jmf\TemplateRendering\TemplateInterface;
use Webmozart\Assert\Assert;

readonly class ColumnDefinitionCompiler
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
    public function compile(array $columnConfig): ColumnDefinition
    {
        Assert::isMap($columnConfig);

        $columnDefinition = new ColumnDefinition(
            $this->getAlign($columnConfig),
            $this->getLabel($columnConfig),
            $this->getSource($columnConfig),
            $this->getTemplate($columnConfig),
            $this->getPresetId($columnConfig),
        );

        return $this->presetApplier->apply($columnDefinition);
    }

    /**
     * @param array<string, mixed> $columnConfig
     *
     * @return null|non-empty-string
     */
    private function getAlign(array $columnConfig): ?string
    {
        $align = $columnConfig['align'] ?? null;

        Assert::nullOrStringNotEmpty($align);

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
