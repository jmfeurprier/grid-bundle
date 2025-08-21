<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Jmf\Grid\Configuration\Grid\GridConfiguration;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;
use Jmf\TemplateRendering\TemplateRendererInterface;

readonly class RowLinkGenerator
{
    public function __construct(
        private TemplateRendererInterface $templateRenderer,
    ) {
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $rowVariables
     * @param array<string, mixed>        $arguments
     *
     * @throws TemplateRenderingException
     */
    public function generate(
        GridConfiguration $gridConfiguration,
        array | object $item,
        array $rowVariables,
        array $arguments,
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
     * @throws TemplateRenderingException
     */
    private function renderTemplateFromString(
        string $template,
        array $context,
    ): string {
        return $this->templateRenderer->renderFromString($template, $context);
    }
}
