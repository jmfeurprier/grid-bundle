<?php

namespace Jmf\Grid\Twig;

use Exception;
use Jmf\Grid\Exception\TemplateRenderingException;
use Jmf\Grid\Grid\GridGenerator;
use Jmf\Grid\TemplateRendering\TemplateRenderer;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GridExtension extends AbstractExtension
{
    public final const string PREFIX_DEFAULT = 'jmf_';

    /**
     * @var array<string, string>
     */
    private const array FUNCTIONS = [
        'grid' => 'grid',
    ];

    public function __construct(
        private readonly GridGenerator $gridGenerator,
        private readonly TemplateRenderer $templateRenderer,
        private readonly string $templatePath,
        private readonly string $prefix = self::PREFIX_DEFAULT,
    ) {
    }

    /**
     * @return TwigFunction[]
     */
    #[Override]
    public function getFunctions(): iterable
    {
        $functions = [];

        foreach (self::FUNCTIONS as $function => $method) {
            $functions[] = new TwigFunction(
                ($this->prefix . $function),
                [
                    $this,
                    $method,
                ],
                [
                    'is_safe' => ['html'],
                ]
            );
        }

        return $functions;
    }

    /**
     * @param list<array<string, mixed>|object> $items
     * @param array<string, mixed>              $arguments
     * @param array<string, mixed>              $parameters
     *
     * @throws Exception
     * @throws TemplateRenderingException
     */
    public function grid(
        string $gridId,
        array $items,
        array $arguments = [],
        array $parameters = []
    ): string {
        return $this->templateRenderer->renderFromFile(
            $this->templatePath,
            $parameters + [
                'grid' => $this->gridGenerator->generate($gridId, $items, $arguments),
            ]
        );
    }
}
