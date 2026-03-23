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
     * @param array<string, mixed> $rowVariables
     *
     * @throws TemplateRenderingException
     */
    public function generate(
        GridConfiguration $gridConfiguration,
        array $rowVariables,
    ): ?string {
        $link = $gridConfiguration->getRowConfiguration()->getLink();

        if (null === $link) {
            return null;
        }

        return $this->renderTemplateFromString(
            $link,
            $rowVariables,
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
