<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Footer;

use Jmf\Grid\Grid\Footer\FooterDefinition;
use Jmf\Grid\Grid\GridDefinition;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;
use Jmf\TemplateRendering\TemplateInterface;
use Jmf\TemplateRendering\TemplateRendererInterface;

readonly class FooterGenerator
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
        GridDefinition $gridDefinition,
        iterable $items,
        array $arguments,
    ): Footer {
        $rows = [];

        foreach ($gridDefinition->getFooterDefinitions() as $footerRowDefinitions) {
            $cells = [];

            foreach ($footerRowDefinitions as $footerColumnDefinition) {
                $cells[] = new FooterCell(
                    $this->buildValue($footerColumnDefinition, $items, $arguments),
                    $this->buildAttributes($footerColumnDefinition),
                );
            }

            $rows[] = new FooterRow($cells);
        }

        return new Footer($rows);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildAttributes(FooterDefinition $footerDefinition): array
    {
        $attributes = [];
        $classes    = [];

        // @todo Too "bootstrapy". Move to dedicated field in FooterCell.
        if (null !== $footerDefinition->getAlign()) {
            $classes[] = "text-{$footerDefinition->getAlign()}";
        }

        if ([] !== $classes) {
            $attributes['class'] = implode(' ', $classes);
        }

        $merge = $footerDefinition->getMerge() ?? 1;

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
        FooterDefinition $footerDefinition,
        iterable $items,
        array $arguments,
    ): string {
        $value = '';

        if (null !== $footerDefinition->getValue()) {
            $value = $footerDefinition->getValue();
        } elseif ($footerDefinition->getTemplate() instanceof TemplateInterface) {
            $context = array_merge(
                $arguments,
                [
                    '_items' => $items,
                ],
            );

            $value = $this->templateRenderer->render(
                $footerDefinition->getTemplate(),
                $context,
            );
        }

        return trim($value);
    }
}
