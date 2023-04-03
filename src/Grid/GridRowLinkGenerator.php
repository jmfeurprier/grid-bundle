<?php

namespace Jmf\Grid\Grid;

use Exception;
use RuntimeException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\TemplateWrapper;

class GridRowLinkGenerator
{
    private TwigEnvironment $twigEnvironment;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        TwigEnvironment $twigEnvironment,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->twigEnvironment = $twigEnvironment;
        $this->urlGenerator    = $urlGenerator;
    }

    /**
     * @param object|array $item
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function generate(
        GridDefinition $gridDefinition,
        $item,
        array $rowVariables,
        array $arguments
    ): ?string {
        $link = $gridDefinition->getRowsLink();

        if (null === $link) {
            return null;
        }

        $context = $arguments + $rowVariables + [
                '_item' => $item,
            ];

        if (is_string($link)) {
            return $this->renderTemplateFromString(
                $link,
                $context
            );
        }

        if (is_array($link)) {
            $route = $link['route'] ?? null;

            if (!is_array($route)) {
                throw new RuntimeException('Missing or invalid link route definition.');
            }

            $routeName = $route['name'] ?? null;

            if (!is_string($routeName)) {
                throw new RuntimeException('Missing or invalid link route name definition.');
            }

            $routeParameters = [];

            foreach ($route['parameters'] ?? [] as $parameter => $template) {
                $routeParameters[$parameter] = $this->renderTemplateFromString($template, $context);
            }

            return $this->urlGenerator->generate($routeName, $routeParameters);
        }

        throw new RuntimeException('Invalid link definition.');
    }

    private function renderTemplateFromString(
        string $template,
        array $context = []
    ): string {
        return $this->createTemplate($template)->render($context);
    }

    /**
     * @throws LoaderError
     * @throws SyntaxError
     */
    protected function createTemplate(string $template): TemplateWrapper
    {
        return $this->twigEnvironment->createTemplate($template);
    }
}
