<?php

namespace Jmf\Grid\Twig;

use Jmf\Grid\Grid\GridGenerator;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GridExtension extends AbstractExtension
{
    public const PREFIX_DEFAULT = 'jmf_';

    private const FUNCTIONS = [
        'grid' => 'grid',
    ];

    private GridGenerator $gridGenerator;

    private TwigEnvironment $twigEnvironment;

    private string $templatePath;

    private string $prefix;

    public function __construct(
        GridGenerator $gridGenerator,
        TwigEnvironment $twigEnvironment,
        string $templatePath,
        string $prefix = self::PREFIX_DEFAULT
    ) {
        $this->gridGenerator   = $gridGenerator;
        $this->twigEnvironment = $twigEnvironment;
        $this->templatePath    = $templatePath;
        $this->prefix          = $prefix;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
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

    public function grid(
        string $gridId,
        iterable $items,
        array $arguments = [],
        array $parameters = []
    ): string {
        return $this->renderView(
            $this->templatePath,
            $parameters + [
                'grid' => $this->gridGenerator->generate($gridId, $items, $arguments),
            ]
        );
    }

    private function renderView(
        string $view,
        array $parameters
    ): string {
        return $this->twigEnvironment->render($view, $parameters);
    }
}
