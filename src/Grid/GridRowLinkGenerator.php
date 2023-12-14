<?php

namespace Jmf\Grid\Grid;

use Jmf\Grid\Configuration\GridConfiguration;
use Jmf\Grid\TemplateRendering\TemplateRenderer;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;

readonly class GridRowLinkGenerator
{
    public function __construct(
        private TemplateRenderer $templateRenderer,
    ) {
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $rowVariables
     * @param array<string, mixed>        $arguments
     */
    public function generate(
        GridConfiguration $gridConfiguration,
        array | object $item,
        array $rowVariables,
        array $arguments
    ): ?string {
        $link = $gridConfiguration->getRowConfiguration()->getLink();

        if (null === $link) {
            return null;
        }

        $context = $arguments + $rowVariables + [
                '_item' => $item,
            ];

        return $this->renderTemplateFromString(
            $link,
            $context,
        );
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws LoaderError
     * @throws SyntaxError
     */
    private function renderTemplateFromString(
        string $template,
        array $context,
    ): string {
        return $this->templateRenderer->renderFromString($template, $context);
    }
}
