<?php

declare(strict_types=1);

namespace Jmf\Grid\Twig;

use Jmf\Grid\Exception\GridException;
use Jmf\Grid\Grid\GridGenerator;
use Jmf\RenderingPreset\Exception\RenderingPresetException;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GridExtension extends AbstractExtension
{
    public final const string PREFIX_DEFAULT = '';

    public function __construct(
        private readonly GridGenerator $gridGenerator,
        private readonly TemplateRendererInterface $templateRenderer,
        private readonly string $templatePath,
        private readonly string $prefix = self::PREFIX_DEFAULT,
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    #[Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                $this->prefix . 'grid',
                $this->grid(...),
                [
                    'is_safe' => ['html'],
                ],
            ),
        ];
    }

    /**
     * @param non-empty-string                  $gridId
     * @param list<array<string, mixed>|object> $items
     * @param array<string, mixed>              $arguments
     * @param array<string, mixed>              $parameters
     *
     * @throws GridException
     * @throws RenderingPresetException
     * @throws TemplateRenderingException
     */
    public function grid(
        string $gridId,
        array $items,
        array $arguments = [],
        array $parameters = [],
    ): string {
        return $this->templateRenderer->renderFromFile(
            $this->templatePath,
            $parameters + [
                'grid' => $this->gridGenerator->generate($gridId, $items, $arguments),
            ],
        );
    }
}
