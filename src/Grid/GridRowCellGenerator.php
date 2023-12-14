<?php

namespace Jmf\Grid\Grid;

use Exception;
use Jmf\Grid\Configuration\ColumnConfiguration;
use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\TemplateWrapper;
use Webmozart\Assert\Assert;

class GridRowCellGenerator
{
    private ColumnConfiguration $columnConfiguration;

    /**
     * @var array<string, mixed>|object
     */
    private array | object $item;

    /**
     * @var array<string, mixed>
     */
    private array $rowVariables;

    /**
     * @param array<string, string> $macros
     */
    public function __construct(
        private readonly TwigEnvironment $twigEnvironment,
        private readonly PropertyAccessor $propertyAccessor,
        private readonly array $macros = [],
    ) {
        Assert::isMap($this->macros);
        Assert::allString($this->macros);
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $rowVariables
     *
     * @throws Exception
     */
    public function generate(
        ColumnConfiguration $columnConfiguration,
        array | object $item,
        array $rowVariables,
    ): GridRowCell {
        $this->init(
            $columnConfiguration,
            $item,
            $rowVariables
        );

        return $this->buildCell();
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $rowVariables
     */
    private function init(
        ColumnConfiguration $columnConfiguration,
        array | object $item,
        array $rowVariables
    ): void {
        $this->columnConfiguration = $columnConfiguration;
        $this->item                = $item;
        $this->rowVariables        = $rowVariables;
    }

    private function buildCell(): GridRowCell
    {
        return new GridRowCell(
            $this->getCellValue(),
            $this->getCellParameters(),
        );
    }

    private function getCellValue(): string
    {
        $value = '';

        if (null !== $this->columnConfiguration->getSource()) {
            $source = $this->columnConfiguration->getSource();

            if (is_array($this->item)) {
                $value = $this->item[$source] ?? '';
            } elseif (is_object($this->item)) {
                $value = $this->propertyAccessor->getValue($this->item, $source);
            } else {
                throw new RuntimeException('Unexpected item type.');
            }
        }

        if (null !== $this->columnConfiguration->getTemplate()) {
            $context = $this->rowVariables + [
                    '_value' => $value,
                ];

            $value = $this->getColumnTemplate()->render($context);
        }

        Assert::string($value);

        return trim($value);
    }

    /**
     * @return array<string, mixed>
     */
    private function getCellParameters(): array
    {
        $parameters = [];

        if (null !== $this->columnConfiguration->getAlign()) {
            $parameters['align'] = $this->columnConfiguration->getAlign();
        }

        return $parameters;
    }

    private function getColumnTemplate(): TemplateWrapper
    {
        static $cache = [];

        $template = $this->columnConfiguration->getTemplate();

        if (null === $template) {
            throw new RuntimeException();
        }

        $cacheKey = serialize($template);

        if (!array_key_exists($cacheKey, $cache)) {
            $templateChunks = [];

            foreach ($this->macros as $macroAlias => $macroPath) {
                $templateChunks[] = "{% import '{$macroPath}' as {$macroAlias} %}";
            }

            $templateChunks[] = $template;

            $templateWrapper = $this->createTemplate(
                implode(
                    "\n",
                    $templateChunks,
                )
            );

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
