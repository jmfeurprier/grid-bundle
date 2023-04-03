<?php

namespace Jmf\Grid\Grid;

use Exception;
use RuntimeException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\TemplateWrapper;

class GridFooterGenerator
{
    private TwigEnvironment $twigEnvironment;

    private array $entityRenderingPresets;

    private array $footerDefinition;

    private iterable $items;

    private array $arguments;

    public function __construct(
        TwigEnvironment $twigEnvironment,
        array $entityRenderingPresets
    ) {
        $this->twigEnvironment        = $twigEnvironment;
        $this->entityRenderingPresets = $entityRenderingPresets;
    }

    /**
     * @param object[] $items
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function generate(
        GridDefinition $gridDefinition,
        iterable $items,
        array $arguments
    ): array {
        $this->init($gridDefinition, $items, $arguments);

        $this->applyPresets();

        return $this->buildFooter();
    }

    private function init(
        GridDefinition $gridDefinition,
        iterable $items,
        array $arguments
    ): void {
        $this->footerDefinition = $gridDefinition->getFooter();
        $this->items            = $items;
        $this->arguments        = $arguments;
    }

    private function applyPresets(): void
    {
        foreach ($this->footerDefinition as $rowKey => $footerRowDefinition) {
            foreach ($footerRowDefinition as $columnKey => $footerColumnDefinition) {
                $this->footerDefinition[$rowKey][$columnKey] = $this->applyPresetToColumnDefinition(
                    $footerColumnDefinition
                );
            }
        }
    }

    /**
     * @throws RuntimeException
     */
    private function applyPresetToColumnDefinition(array $footerColumnDefinition): array
    {
        if (empty($footerColumnDefinition['preset'])) {
            return $footerColumnDefinition;
        }

        $presetId = $footerColumnDefinition['preset'];
        unset($footerColumnDefinition['preset']);

        $newColumnDefinition = array_merge(
            $this->getPreset($presetId),
            $footerColumnDefinition
        );

        return $this->applyPresetToColumnDefinition($newColumnDefinition);
    }

    /**
     * @throws RuntimeException
     */
    private function getPreset(string $presetId): array
    {
        if (isset($this->entityRenderingPresets[$presetId])) {
            return $this->entityRenderingPresets[$presetId];
        }

        throw new RuntimeException("Table footer column preset '{$presetId}' not defined.");
    }

    private function buildFooter(): array
    {
        $output = [];

        foreach ($this->footerDefinition as $footerRowDefinition) {
            $outputRow = [];

            foreach ($footerRowDefinition as $footerColumnDefinition) {
                $outputColumn = [
                    'value' => $this->buildValue($footerColumnDefinition),
                ];

                $attributes = $this->buildAttributes($footerColumnDefinition);
                if (!empty($attributes)) {
                    $outputColumn['attributes'] = $attributes;
                }

                $outputRow[] = $outputColumn;
            }

            $output[] = $outputRow;
        }

        return $output;
    }

    private function buildAttributes(array $footerColumnDefinition): array
    {
        $attributes = [];

        $classes = [];
        if (array_key_exists('align', $footerColumnDefinition)) {
            $classes[] = "text-{$footerColumnDefinition['align']}";
        }

        if (!empty($classes)) {
            $attributes['class'] = join(' ', $classes);
        }

        $merge = $footerColumnDefinition['merge'] ?? 1;
        if ($merge > 1) {
            $attributes['colspan'] = $merge;
        }

        return $attributes;
    }

    /**
     * @throws SyntaxError
     * @throws LoaderError
     */
    private function buildValue(array $footerColumnDefinition): string
    {
        $value = '';

        if (!empty($footerColumnDefinition['value'])) {
            $value = $footerColumnDefinition['value'];
        } elseif (!empty($footerColumnDefinition['template'])) {
            $template = $this->createTemplate($footerColumnDefinition['template']);

            $context = $this->arguments + [
                    '_items' => $this->items,
                ];

            $value = $template->render($context);
        }

        return trim($value);
    }

    /**
     * @throws LoaderError
     * @throws SyntaxError
     */
    protected function createTemplate(
        string $template,
        ?string $name = null
    ): TemplateWrapper {
        return $this->twigEnvironment->createTemplate($template, $name);
    }
}
