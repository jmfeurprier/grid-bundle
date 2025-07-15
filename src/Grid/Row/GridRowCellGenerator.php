<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Jmf\Grid\Configuration\ColumnConfiguration;
use Jmf\Grid\Exception\GridException;
use Stringable;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Throwable;
use Twig\Environment as TwigEnvironment;
use Twig\TemplateWrapper;

// @todo Rewrite without Twig dependency and local cache.
readonly class GridRowCellGenerator
{
    public function __construct(
        private TwigEnvironment $twigEnvironment,
        private PropertyAccessorInterface $propertyAccessor,
    ) {
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
        return new GridRowCell(
            $this->getCellValue($columnConfiguration, $item, $rowVariables),
            $this->getCellParameters($columnConfiguration),
        );
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $rowVariables
     *
     * @throws GridException
     */
    private function getCellValue(
        ColumnConfiguration $columnConfiguration,
        array | object $item,
        array $rowVariables,
    ): string {
        $value  = null;
        $source = $columnConfiguration->getSource();

        if (null !== $source) {
            $value = $this->getItemValue($item, $source);
        }

        $template = $columnConfiguration->getTemplate();

        if (null !== $template) {
            $context = array_merge(
                $rowVariables,
                [
                    '_value' => $value,
                ],
            );

            $value = $this->getColumnTemplate($template)->render($context);
        }

        return trim($this->getStringValue($value));
    }

    /**
     * @param array<string, mixed>|object $item
     */
    private function getItemValue(
        array | object $item,
        string $source,
    ): mixed {
        if (is_array($item)) {
            return $item[$source] ?? null;
        }

        return $this->propertyAccessor->getValue($item, $source);
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
    private function getCellParameters(ColumnConfiguration $columnConfiguration): array
    {
        $parameters = [];

        if (null !== $columnConfiguration->getAlign()) {
            $parameters['align'] = $columnConfiguration->getAlign();
        }

        return $parameters;
    }

    /**
     * @throws GridException
     */
    private function getColumnTemplate(string $template): TemplateWrapper
    {
        return $this->createTemplate(
            $template,
        );
        /*
                static $cache = [];

                $cacheKey = serialize($template);

                if (!array_key_exists($cacheKey, $cache)) {
                    $templateWrapper = $this->createTemplate(
                        $template,
                    );

                    $cache[$cacheKey] = $templateWrapper;
                }

                return $cache[$cacheKey];
        */
    }

    /**
     * @throws GridException
     */
    protected function createTemplate(string $template): TemplateWrapper
    {
        try {
            return $this->twigEnvironment->createTemplate($template);
        } catch (Throwable $e) {
            throw new GridException(
                message:  'Failed creating template.',
                previous: $e,
            );
        }
    }
}
