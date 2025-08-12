<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Footer;

use Jmf\Grid\Configuration\FooterConfiguration;
use Jmf\Grid\Configuration\GridConfiguration;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;
use Jmf\TemplateRendering\TemplateInterface;
use Jmf\TemplateRendering\TemplateRendererInterface;

readonly class GridFooterGenerator
{
    public function __construct(
        private TemplateRendererInterface $templateRenderer,
    ) {
    }

    /**
     * @param iterable<array<string, mixed>|object> $items
     * @param array<string, mixed>                  $arguments
     *
     * @throws TemplateRenderingException
     */
    public function generate(
        GridConfiguration $gridConfiguration,
        iterable $items,
        array $arguments,
    ): GridFooter {
        $rows = [];

        foreach ($gridConfiguration->getFooterConfigurations() as $footerRowConfiguration) {
            $cells = [];

            foreach ($footerRowConfiguration as $footerColumnConfiguration) {
                $cells[] = new GridFooterCell(
                    $this->buildValue($footerColumnConfiguration, $items, $arguments),
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

        // @todo Too "bootstrapy". Move to dedicated field in GridFooterCell.
        if (null !== $footerConfiguration->getAlign()) {
            $classes[] = "text-{$footerConfiguration->getAlign()}";
        }

        if ([] !== $classes) {
            $attributes['class'] = implode(' ', $classes);
        }

        $merge = $footerConfiguration->getMerge() ?? 1;

        if ($merge > 1) {
            $attributes['colspan'] = $merge;
        }

        return $attributes;
    }

    /**
     * @param iterable<array<string, mixed>|object> $items
     * @param array<string, mixed>                  $arguments
     *
     * @throws TemplateRenderingException
     */
    private function buildValue(
        FooterConfiguration $footerConfiguration,
        iterable $items,
        array $arguments,
    ): string {
        $value = '';

        if (null !== $footerConfiguration->getValue()) {
            $value = $footerConfiguration->getValue();
        } elseif ($footerConfiguration->getTemplate() instanceof TemplateInterface) {
            $context = array_merge(
                $arguments,
                [
                    '_items' => $items,
                ],
            );

            $value = $this->templateRenderer->render(
                $footerConfiguration->getTemplate(),
                $context,
            );
        }

        return trim($value);
    }
}
