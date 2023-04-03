<?php

namespace Jmf\Grid;

use Exception;
use RuntimeException;
use Twig\Environment as TwigEnvironment;
use Twig\Error\LoaderError;
use Twig\Error\SyntaxError;
use Twig\TemplateWrapper;

class GridRowGenerator
{
    private const MACROS_MAPPING = [
        'macro_entity' => '_macro/entity.html.twig',
    ];

    private TwigEnvironment $twigEnvironment;

    private GridRowCellGenerator $gridRowCellGenerator;

    private GridRowLinkGenerator $gridRowLinkGenerator;

    private GridDefinition $gridDefinition;

    private $item;

    private int $rowIndex;

    private int $rowCount;

    private array $arguments;

    private array $rowVariables;

    public function __construct(
        TwigEnvironment $twigEnvironment,
        GridRowCellGenerator $gridRowCellGenerator,
        GridRowLinkGenerator $gridRowLinkGenerator
    ) {
        $this->twigEnvironment      = $twigEnvironment;
        $this->gridRowCellGenerator = $gridRowCellGenerator;
        $this->gridRowLinkGenerator = $gridRowLinkGenerator;
    }

    /**
     * @param object|array $item
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function generate(
        GridDefinition $gridDefinition,
        $item,
        int $rowIndex,
        int $rowCount,
        array $arguments
    ): array {
        $this->init($gridDefinition, $item, $rowIndex, $rowCount, $arguments);

        $this->buildRowVariables();

        return $this->buildRow();
    }

    /**
     * @param object|array $item
     */
    private function init(
        GridDefinition $gridDefinition,
        $item,
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

        foreach ($this->gridDefinition->getRowsVariables() as $key => $value) {
            $rowVariables[$key] = $this->renderTemplateFromString(
                '{% import "_macro/entity.html.twig" as macro_entity %}' . $value,
                $rowVariables
            );
        }

        // @xxx Safety to prevent overwrites.
        $rowVariables['_item'] = $this->item;
        $rowVariables['_loop'] = $loopVariables;

        $this->rowVariables = $rowVariables;
    }

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

    private function buildRowCells(): array
    {
        $cells = [];

        foreach ($this->gridDefinition->getColumns() as $columnDefinition) {
            $cells[] = $this->buildCell($columnDefinition);
        }

        return $cells;
    }

    private function buildCell(array $columnDefinition): array
    {
        return $this->gridRowCellGenerator->generate(
            $columnDefinition,
            $this->item,
            $this->rowVariables
        );
    }

    private function buildRowLink(): ?string
    {
        return $this->gridRowLinkGenerator->generate(
            $this->gridDefinition,
            $this->item,
            $this->rowVariables,
            $this->arguments
        );
    }

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
