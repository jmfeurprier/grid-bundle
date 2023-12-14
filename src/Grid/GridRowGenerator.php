<?php

namespace Jmf\Grid\Grid;

use Exception;
use Jmf\Grid\Configuration\ColumnConfiguration;
use Jmf\Grid\Configuration\GridConfiguration;
use Jmf\Grid\Exception\TemplateRenderingException;
use Jmf\Grid\TemplateRendering\TemplateRenderer;
use Webmozart\Assert\Assert;

class GridRowGenerator
{
    private GridConfiguration $gridConfiguration;

    /**
     * @var array<string, mixed>|object
     */
    private array | object $item;

    private int $rowIndex;

    private int $rowCount;

    /**
     * @var array<string, mixed>
     */
    private array $arguments;

    /**
     * @var array<string, mixed>
     */
    private array $rowVariables;

    /**
     * @param array<string, string> $macros
     */
    public function __construct(
        private readonly TemplateRenderer $templateRenderer,
        private readonly GridRowCellGenerator $gridRowCellGenerator,
        private readonly GridRowLinkGenerator $gridRowLinkGenerator,
        private readonly array $macros = [],
    ) {
        Assert::isMap($this->macros);
        Assert::allString($this->macros);
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $arguments
     *
     * @throws Exception
     */
    public function generate(
        GridConfiguration $gridConfiguration,
        array | object $item,
        int $rowIndex,
        int $rowCount,
        array $arguments
    ): GridRow {
        $this->init($gridConfiguration, $item, $rowIndex, $rowCount, $arguments);

        $this->buildRowVariables();

        return $this->buildRow();
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $arguments
     */
    private function init(
        GridConfiguration $gridConfiguration,
        array | object $item,
        int $rowIndex,
        int $rowCount,
        array $arguments
    ): void {
        $this->gridConfiguration = $gridConfiguration;
        $this->item              = $item;
        $this->rowIndex          = $rowIndex;
        $this->rowCount          = $rowCount;
        $this->arguments         = $arguments;
    }

    /**
     * @throws Exception
     * @throws TemplateRenderingException
     */
    private function buildRow(): GridRow
    {
        return new GridRow(
            $this->buildRowCells(),
            $this->buildRowLink(),
        );
    }

    /**
     * @throws TemplateRenderingException
     */
    private function buildRowVariables(): void
    {
        $loopVariables         = $this->buildLoopVariable();
        $rowVariables          = $this->gridConfiguration->getGridVariables()->all() + $this->arguments;
        $rowVariables['_item'] = $this->item;
        $rowVariables['_loop'] = $loopVariables;

        $macroChunks = [];
        foreach ($this->macros as $macroAlias => $macroPath) {
            $macroChunks[] = "{% import '{$macroPath}' as {$macroAlias} %}";
        }

        foreach ($this->gridConfiguration->getRowConfiguration()->getVariables()->all() as $key => $value) {
            $rowVariables[$key] = $this->renderTemplateFromString(
                implode('', $macroChunks) . $value,
                $rowVariables
            );
        }

        // @xxx Safety to prevent overwrites.
        $rowVariables['_item'] = $this->item;
        $rowVariables['_loop'] = $loopVariables;

        $this->rowVariables = $rowVariables;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildLoopVariable(): array
    {
        return [
            'index'     => $this->rowIndex,
            'index0'    => ($this->rowIndex - 1),
            'revindex0' => ($this->rowCount - $this->rowIndex),
            'revindex'  => ($this->rowCount - $this->rowIndex + 1),
            'first'     => (1 === $this->rowIndex),
            'last'      => ($this->rowCount === $this->rowIndex),
            'length'    => $this->rowCount,
            'parent'    => null,
        ];
    }

    /**
     * @return GridRowCell[]
     *
     * @throws Exception
     */
    private function buildRowCells(): iterable
    {
        $cells = [];

        foreach ($this->gridConfiguration->getColumnConfigurations() as $columnConfiguration) {
            $cells[] = $this->buildCell($columnConfiguration);
        }

        return $cells;
    }

    /**
     * @throws Exception
     */
    private function buildCell(ColumnConfiguration $columnConfiguration): GridRowCell
    {
        return $this->gridRowCellGenerator->generate(
            $columnConfiguration,
            $this->item,
            $this->rowVariables
        );
    }

    private function buildRowLink(): ?string
    {
        return $this->gridRowLinkGenerator->generate(
            $this->gridConfiguration,
            $this->item,
            $this->rowVariables,
            $this->arguments
        );
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws TemplateRenderingException
     */
    private function renderTemplateFromString(
        string $template,
        array $context,
    ): string {
        return $this->templateRenderer->renderFromString($template, $context);
    }
}
