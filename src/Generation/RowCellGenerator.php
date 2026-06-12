<?php

declare(strict_types=1);

namespace Jmf\Grid\Generation;

use Jmf\Grid\Definition\ColumnDefinition;
use Jmf\Grid\Exception\UnexpectedValueTypeException;
use Jmf\Grid\Model\RowCell;
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
        ColumnDefinition $columnDefinition,
        array | object $item,
        array $rowVariables,
    ): RowCell {
        return new RowCell(
            $this->getCellValue($columnDefinition, $item, $rowVariables),
            $columnDefinition->getAlign(),
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
        ColumnDefinition $columnDefinition,
        array | object $item,
        array $rowVariables,
    ): string {
        $value  = null;
        $source = $columnDefinition->getSource();

        if (null !== $source) {
            $value = $this->propertyAccessor->getValue($item, $source);
        }

        $template = $columnDefinition->getTemplate();

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

        throw new UnexpectedValueTypeException(get_debug_type($value));
    }


}
