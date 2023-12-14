<?php

namespace Jmf\Grid\Grid;

use Exception;
use Jmf\Grid\Configuration\FooterConfiguration;
use Jmf\Grid\Configuration\GridConfiguration;
use Jmf\Grid\Exception\TemplateRenderingException;
use Jmf\Grid\TemplateRendering\TemplateRenderer;

class GridFooterGenerator
{
    /**
     * @var FooterConfiguration[][]
     */
    private iterable $footerConfigurations;

    /**
     * @var iterable<array<string, mixed>|object>
     */
    private iterable $items;

    /**
     * @var array<string, mixed>
     */
    private array $arguments;

    public function __construct(
        private readonly TemplateRenderer $templateRenderer,
    ) {
    }

    /**
     * @param iterable<array<string, mixed>|object> $items
     * @param array<string, mixed>                  $arguments
     *
     * @throws Exception
     */
    public function generate(
        GridConfiguration $gridConfiguration,
        iterable $items,
        array $arguments
    ): GridFooter {
        $this->init($gridConfiguration, $items, $arguments);

        return $this->buildFooter();
    }

    /**
     * @param iterable<array<string, mixed>|object> $items
     * @param array<string, mixed>                  $arguments
     */
    private function init(
        GridConfiguration $gridConfiguration,
        iterable $items,
        array $arguments
    ): void {
        $this->footerConfigurations = $gridConfiguration->getFooterConfigurations();
        $this->items                = $items;
        $this->arguments            = $arguments;
    }

    /**
     * @throws TemplateRenderingException
     */
    private function buildFooter(): GridFooter
    {
        $rows = [];

        foreach ($this->footerConfigurations as $footerRowConfiguration) {
            $cells = [];

            foreach ($footerRowConfiguration as $footerColumnConfiguration) {
                $cells[] = new GridFooterCell(
                    $this->buildValue($footerColumnConfiguration),
                    $this->buildAttributes($footerColumnConfiguration),
                );
            }

            $rows[] = new GridFooterRow($cells);
        }

        return new GridFooter($rows);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAttributes(FooterConfiguration $footerConfiguration): array
    {
        $attributes = [];
        $classes    = [];

        if (null !== $footerConfiguration->getAlign()) {
            $classes[] = "text-{$footerConfiguration->getAlign()}";
        }

        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }

        $merge = $footerConfiguration->getMerge() ?? 1;

        if ($merge > 1) {
            $attributes['colspan'] = $merge;
        }

        return $attributes;
    }

    /**
     * @throws TemplateRenderingException
     */
    private function buildValue(FooterConfiguration $footerConfiguration): string
    {
        $value = '';

        if (null !== $footerConfiguration->getValue()) {
            $value = $footerConfiguration->getValue();
        } elseif (null !== $footerConfiguration->getTemplate()) {
            $context = $this->arguments + [
                    '_items' => $this->items,
                ];

            $value = $this->templateRenderer->renderFromString(
                $footerConfiguration->getTemplate(),
                $context,
            );
        }

        return trim($value);
    }
}
