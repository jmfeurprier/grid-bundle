<?php

namespace Jmf\Grid\Grid;

use Exception;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\TemplateWrapper;

class GridRowCellGenerator
{
    private const MACROS_MAPPING = [
        'macro_entity' => '_macro/entity.html.twig',
    ];

    private PropertyAccessor $propertyAccessor;

    private array $columnDefinition;

    private TwigEnvironment $twigEnvironment;

    private $item;

    private array $rowVariables;

    public function __construct(
        TwigEnvironment $twigEnvironment,
        PropertyAccessor $propertyAccessor
    ) {
        $this->twigEnvironment  = $twigEnvironment;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param object|array $item
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function generate(
        array $columnDefinition,
        $item,
        array $rowVariables
    ): array {
        $this->init(
            $columnDefinition,
            $item,
            $rowVariables
        );

        return $this->buildCell();
    }

    /**
     * @param object|array $item
     */
    private function init(
        array $columnDefinition,
        $item,
        array $rowVariables
    ): void {
        $this->columnDefinition = $columnDefinition;
        $this->item             = $item;
        $this->rowVariables     = $rowVariables;
    }

    private function buildCell(): array
    {
        return [
            'value'      => $this->getCellValue(),
            'parameters' => $this->getCellParameters(),
        ];
    }

    /**
     * @throws RuntimeException
     */
    private function getCellValue(): string
    {
        $value = '';

        if (!empty($this->columnDefinition['source'])) {
            $source = $this->columnDefinition['source'];

            if (is_array($this->item)) {
                $value = $this->item[$source] ?? '';
            } elseif (is_object($this->item)) {
                $value = $this->propertyAccessor->getValue($this->item, $source);
            } else {
                throw new RuntimeException('Unexpected item type.');
            }
        }

        if (!empty($this->columnDefinition['template'])) {
            $context = $this->rowVariables + [
                    '_value' => $value,
                ];

            $value = $this->getColumnTemplate()->render($context);
        }

        return trim($value);
    }

    private function getCellParameters(): array
    {
        $parameters = [];

        if (array_key_exists('align', $this->columnDefinition)) {
            $parameters['align'] = $this->columnDefinition['align'];
        }

        return $parameters;
    }

    private function getColumnTemplate(): TemplateWrapper
    {
        static $cache = [];

        if (!array_key_exists('template', $this->columnDefinition)) {
            throw new RuntimeException();
        }

        $cacheKey = serialize($this->columnDefinition['template']);

        if (!array_key_exists($cacheKey, $cache)) {
            $templateChunks = [];

            foreach (self::MACROS_MAPPING as $macroAlias => $macroPath) {
                $templateChunks[] = "{% import '{$macroPath}' as {$macroAlias} %}";
            }

            $templateChunks[] = $this->columnDefinition['template'];

            $template = implode("\n", $templateChunks);

            $templateWrapper = $this->createTemplate($template);

            $cache[$cacheKey] = $templateWrapper;
        }

        return $cache[$cacheKey];
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
