<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Jmf\Grid\Configuration\Column\ColumnConfiguration;
use Jmf\Grid\Configuration\Grid\GridConfiguration;
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
        GridConfiguration $gridConfiguration,
        array | object $item,
        int $rowIndex,
        int $rowCount,
        array $arguments,
    ): Row {
        $rowVariables = $this->buildRowVariables(
            $gridConfiguration,
            $item,
            $rowIndex,
            $rowCount,
            $arguments,
        );

        return new Row(
            $this->buildRowCells($gridConfiguration, $item, $rowVariables),
            $this->buildRowLink($gridConfiguration, $rowVariables),
            $this->buildRowAttributes($gridConfiguration, $rowVariables),
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
        GridConfiguration $gridConfiguration,
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
            $gridConfiguration->getGridVariables()->all(),
            $reservedVariables,
        );

        foreach ($gridConfiguration->getRowConfiguration()->getVariables()->all() as $key => $value) {
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
        GridConfiguration $gridConfiguration,
        array $rowVariables,
    ): array {
        $attributes = [];

        foreach ($gridConfiguration->getRowConfiguration()->getAttributes()->all() as $key => $value) {
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
        GridConfiguration $gridConfiguration,
        array | object $item,
        array $rowVariables,
    ): iterable {
        $cells = [];

        foreach ($gridConfiguration->getColumnConfigurations() as $columnConfiguration) {
            $cells[] = $this->buildCell(
                $columnConfiguration,
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
        ColumnConfiguration $columnConfiguration,
        array | object $item,
        array $rowVariables,
    ): RowCell {
        return $this->gridRowCellGenerator->generate(
            $columnConfiguration,
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
        GridConfiguration $gridConfiguration,
        array $rowVariables,
    ): ?string {
        return $this->gridRowLinkGenerator->generate(
            $gridConfiguration,
            $rowVariables,
        );
    }
}
