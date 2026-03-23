<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Exception\UnexpectedValueTypeException;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;
use Jmf\TemplateRendering\TemplateInterface;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Stringable;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

// @todo Rewrite without Twig dependency and local cache.
readonly class RowCellGenerator
{
    public function __construct(
        private PropertyAccessorInterface $propertyAccessor,
        private TemplateRendererInterface $templateRenderer,
    ) {
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $rowVariables
     *
     * @throws TemplateRenderingException
     * @throws UnexpectedValueTypeException
     */
    public function generate(
        ColumnConfiguration $columnConfiguration,
        array | object $item,
        array $rowVariables,
    ): RowCell {
        return new RowCell(
            $this->getCellValue($columnConfiguration, $item, $rowVariables),
            $this->getCellParameters($columnConfiguration),
        );
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $rowVariables
     *
     * @throws TemplateRenderingException
     * @throws UnexpectedValueTypeException
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

        if ($template instanceof TemplateInterface) {
            $context = array_merge(
                $rowVariables,
                [
                    '_value' => $value,
                ],
            );

            $value = $this->templateRenderer->render($template, $context);
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
     * @throws UnexpectedValueTypeException
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

        throw new UnexpectedValueTypeException();
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
}
