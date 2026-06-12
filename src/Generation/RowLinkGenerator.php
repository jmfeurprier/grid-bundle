<?php

declare(strict_types=1);

namespace Jmf\Grid\Generation;

use Jmf\Grid\Definition\GridDefinition;
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
        GridDefinition $gridDefinition,
        array $rowVariables,
    ): ?string {
        $link = $gridDefinition->getRowDefinition()->getLink();

        if (null === $link) {
            return null;
        }

        return $this->templateRenderer->renderFromString($link, $rowVariables);
    }
}
