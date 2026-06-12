<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Jmf\Grid\Grid\Column\ColumnDefinition;
use Jmf\Grid\Grid\GridDefinition;
use Jmf\Grid\Exception\UnexpectedValueTypeException;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Webmozart\Assert\Assert;

readonly class RowGenerator
{
    public function __construct(
        private TemplateRendererInterface $templateRenderer,
        private RowCellGenerator $gridRowCellGenerator,
        private RowLinkGenerator $gridRowLinkGenerator,
    ) {
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $arguments
     *
     * @throws TemplateRenderingException
     * @throws UnexpectedValueTypeException
     */
    public function generate(
        GridDefinition $gridDefinition,
        array | object $item,
        int $rowIndex,
        int $rowCount,
        array $arguments,
    ): Row {
        $rowVariables = $this->buildRowVariables(
            $gridDefinition,
            $item,
            $rowIndex,
            $rowCount,
            $arguments,
        );

        return new Row(
            $this->buildRowCells($gridDefinition, $item, $rowVariables),
            $this->buildRowLink($gridDefinition, $rowVariables),
            $this->buildRowAttributes($gridDefinition, $rowVariables),
        );
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $arguments
     *
     * @return array<string, mixed>
     *
     * @throws TemplateRenderingException
     */
    private function buildRowVariables(
        GridDefinition $gridDefinition,
        array | object $item,
        int $rowIndex,
        int $rowCount,
        array $arguments,
    ): array {
        $reservedVariables = [
            '_item' => $item,
            '_loop' => $this->buildLoopVariable($rowIndex, $rowCount),
        ];

        $rowVariables = array_merge(
            $arguments,
            $gridDefinition->getGridVariables()->all(),
            $reservedVariables,
        );

        foreach ($gridDefinition->getRowDefinition()->getVariables()->all() as $key => $value) {
            Assert::stringNotEmpty($key);
            Assert::stringNotEmpty($value);

            $rowVariables[$key] = $this->templateRenderer->renderFromString(
                $value,
                $rowVariables,
            );
        }

        // Safety to prevent overwriting reserved variables.
        return array_merge(
            $rowVariables,
            $reservedVariables,
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function buildLoopVariable(
        int $rowIndex,
        int $rowCount,
    ): array {
        return [
            'index'     => $rowIndex,
            'index0'    => ($rowIndex - 1),
            'revindex0' => ($rowCount - $rowIndex),
            'revindex'  => ($rowCount - $rowIndex + 1),
            'first'     => (1 === $rowIndex),
            'last'      => ($rowCount === $rowIndex),
            'length'    => $rowCount,
            'parent'    => null,
        ];
    }

    /**
     * @param array<string, mixed> $rowVariables
     *
     * @return array<string, string>
     *
     * @throws TemplateRenderingException
     */
    private function buildRowAttributes(
        GridDefinition $gridDefinition,
        array $rowVariables,
    ): array {
        $attributes = [];

        foreach ($gridDefinition->getRowDefinition()->getAttributes()->all() as $key => $value) {
            Assert::stringNotEmpty($key);
            Assert::string($value);

            $attributes[$key] = $this->templateRenderer->renderFromString($value, $rowVariables);
        }

        return $attributes;
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $rowVariables
     *
     * @return RowCell[]
     *
     * @throws TemplateRenderingException
     * @throws UnexpectedValueTypeException
     */
    private function buildRowCells(
        GridDefinition $gridDefinition,
        array | object $item,
        array $rowVariables,
    ): iterable {
        $cells = [];

        foreach ($gridDefinition->getColumnDefinitions() as $columnDefinition) {
            $cells[] = $this->buildCell(
                $columnDefinition,
                $item,
                $rowVariables,
            );
        }

        return $cells;
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $rowVariables
     *
     * @throws TemplateRenderingException
     * @throws UnexpectedValueTypeException
     */
    private function buildCell(
        ColumnDefinition $columnDefinition,
        array | object $item,
        array $rowVariables,
    ): RowCell {
        return $this->gridRowCellGenerator->generate(
            $columnDefinition,
            $item,
            $rowVariables,
        );
    }

    /**
     * @param array<string, mixed> $rowVariables
     *
     * @throws TemplateRenderingException
     */
    private function buildRowLink(
        GridDefinition $gridDefinition,
        array $rowVariables,
    ): ?string {
        return $this->gridRowLinkGenerator->generate(
            $gridDefinition,
            $rowVariables,
        );
    }
}
