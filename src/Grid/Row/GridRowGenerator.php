<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Jmf\Grid\Configuration\ColumnConfiguration;
use Jmf\Grid\Configuration\GridConfiguration;
use Jmf\Grid\Exception\GridException;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;
use Jmf\TemplateRendering\TemplateRendererInterface;
use Webmozart\Assert\Assert;

readonly class GridRowGenerator
{
    public function __construct(
        private TemplateRendererInterface $templateRenderer,
        private GridRowCellGenerator $gridRowCellGenerator,
        private GridRowLinkGenerator $gridRowLinkGenerator,
    ) {
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $arguments
     *
     * @throws GridException
     * @throws TemplateRenderingException
     */
    public function generate(
        GridConfiguration $gridConfiguration,
        array | object $item,
        int $rowIndex,
        int $rowCount,
        array $arguments,
    ): GridRow {
        $rowVariables = $this->buildRowVariables(
            $gridConfiguration,
            $item,
            $rowIndex,
            $rowCount,
            $arguments,
        );

        return new GridRow(
            $this->buildRowCells($gridConfiguration, $item, $rowVariables),
            $this->buildRowLink($gridConfiguration, $item, $arguments, $rowVariables),
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
        $loopVariables         = $this->buildLoopVariable($rowIndex, $rowCount);
        $rowVariables          = $gridConfiguration->getGridVariables()->all() + $arguments;
        $rowVariables['_item'] = $item;
        $rowVariables['_loop'] = $loopVariables;

        foreach ($gridConfiguration->getRowConfiguration()->getVariables()->all() as $key => $value) {
            Assert::stringNotEmpty($key);
            Assert::stringNotEmpty($value);

            $rowVariables[$key] = $this->templateRenderer->renderFromString(
                $value,
                $rowVariables,
            );
        }

        // @xxx Safety to prevent overwrites.
        $rowVariables['_item'] = $item;
        $rowVariables['_loop'] = $loopVariables;

        return $rowVariables;
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
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $rowVariables
     *
     * @return GridRowCell[]
     *
     * @throws GridException
     * @throws TemplateRenderingException
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
     * @throws GridException
     * @throws TemplateRenderingException
     */
    private function buildCell(
        ColumnConfiguration $columnConfiguration,
        array | object $item,
        array $rowVariables,
    ): GridRowCell {
        return $this->gridRowCellGenerator->generate(
            $columnConfiguration,
            $item,
            $rowVariables,
        );
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $arguments
     * @param array<string, mixed>        $rowVariables
     *
     * @throws TemplateRenderingException
     */
    private function buildRowLink(
        GridConfiguration $gridConfiguration,
        array | object $item,
        array $arguments,
        array $rowVariables,
    ): ?string {
        return $this->gridRowLinkGenerator->generate(
            $gridConfiguration,
            $item,
            $rowVariables,
            $arguments,
        );
    }
}
