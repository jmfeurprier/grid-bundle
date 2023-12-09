<?php

namespace Jmf\Grid\Grid;

use Exception;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\TemplateWrapper;

class GridRowGenerator
{
    private TwigEnvironment $twigEnvironment;

    private GridRowCellGenerator $gridRowCellGenerator;

    private GridRowLinkGenerator $gridRowLinkGenerator;

    /**
     * @var array<string, string>
     */
    private array $macros;

    private GridDefinition $gridDefinition;

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
        TwigEnvironment $twigEnvironment,
        GridRowCellGenerator $gridRowCellGenerator,
        GridRowLinkGenerator $gridRowLinkGenerator,
        array $macros = []
    ) {
        $this->twigEnvironment      = $twigEnvironment;
        $this->gridRowCellGenerator = $gridRowCellGenerator;
        $this->gridRowLinkGenerator = $gridRowLinkGenerator;
        $this->macros               = $macros;
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $arguments
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function generate(
        GridDefinition $gridDefinition,
        array | object $item,
        int $rowIndex,
        int $rowCount,
        array $arguments
    ): array {
        $this->init($gridDefinition, $item, $rowIndex, $rowCount, $arguments);

        $this->buildRowVariables();

        return $this->buildRow();
    }

    /**
     * @param array<string, mixed>|object $item
     * @param array<string, mixed>        $arguments
     */
    private function init(
        GridDefinition $gridDefinition,
        array | object $item,
        int $rowIndex,
        int $rowCount,
        array $arguments
    ): void {
        $this->gridDefinition = $gridDefinition;
        $this->item           = $item;
        $this->rowIndex       = $rowIndex;
        $this->rowCount       = $rowCount;
        $this->arguments      = $arguments;
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    private function buildRow(): array
    {
        $this->buildRowVariables();

        return [
            'cells' => $this->buildRowCells(),
            'link'  => $this->buildRowLink(),
        ];
    }

    private function buildRowVariables(): void
    {
        $loopVariables         = $this->buildLoopVariable();
        $rowVariables          = $this->gridDefinition->getGridVariables() + $this->arguments;
        $rowVariables['_item'] = $this->item;
        $rowVariables['_loop'] = $loopVariables;

        $macroChunks = [];
        foreach ($this->macros as $macroAlias => $macroPath) {
            $macroChunks[] = "{% import '{$macroPath}' as {$macroAlias} %}";
        }

        foreach ($this->gridDefinition->getRowsVariables() as $key => $value) {
            $rowVariables[$key] = $this->renderTemplateFromString(
                implode($macroChunks) . $value,
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
     * @return iterable<array<string, mixed>>
     *
     * @throws Exception
     */
    private function buildRowCells(): iterable
    {
        $cells = [];

        foreach ($this->gridDefinition->getColumnDefinitions() as $columnDefinition) {
            $cells[] = $this->buildCell($columnDefinition);
        }

        return $cells;
    }

    /**
     * @param array<string, mixed> $columnDefinition
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    private function buildCell(array $columnDefinition): array
    {
        return $this->gridRowCellGenerator->generate(
            $columnDefinition,
            $this->item,
            $this->rowVariables
        );
    }

    /**
     * @throws Exception
     */
    private function buildRowLink(): ?string
    {
        return $this->gridRowLinkGenerator->generate(
            $this->gridDefinition,
            $this->item,
            $this->rowVariables,
            $this->arguments
        );
    }

    /**
     * @param array<string, mixed> $context
     *
     * @throws LoaderError
     * @throws SyntaxError
     */
    private function renderTemplateFromString(
        string $template,
        array $context = []
    ): string {
        return $this->createTemplate($template)->render($context);
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
