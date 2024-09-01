<?php

namespace Jmf\Grid\Grid;

use Jmf\Grid\Configuration\ColumnConfiguration;
use Jmf\Grid\Exception\GridException;
use Stringable;
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
     * @throws GridException
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

    /**
     * @throws GridException
     */
    private function buildCell(): GridRowCell
    {
        return new GridRowCell(
            $this->getCellValue(),
            $this->getCellParameters(),
        );
    }

    /**
     * @throws GridException
     */
    private function getCellValue(): string
    {
        $value = null;

        if (null !== $this->columnConfiguration->getSource()) {
            $source = $this->columnConfiguration->getSource();

            if (is_array($this->item)) {
                $value = $this->item[$source] ?? null;
            } elseif (is_object($this->item)) {
                $value = $this->propertyAccessor->getValue($this->item, $source);
            } else {
                throw new GridException('Unexpected item type.');
            }
        }

        if (null !== $this->columnConfiguration->getTemplate()) {
            $context = $this->rowVariables + [
                    '_value' => $value,
                ];

            $value = $this->getColumnTemplate()->render($context);
        }

        return trim($this->getStringValue($value));
    }

    /**
     * @throws GridException
     */
    private function getStringValue(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (null === $value) {
            return '';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        throw new GridException('Unexpected cell value type.');
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

    /**
     * @throws GridException
     * @throws LoaderError
     * @throws SyntaxError
     */
    private function getColumnTemplate(): TemplateWrapper
    {
        static $cache = [];

        $template = $this->columnConfiguration->getTemplate();

        if (null === $template) {
            throw new GridException();
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
