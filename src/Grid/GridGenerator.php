<?php

namespace Jmf\Grid\Grid;

use DomainException;
use Exception;
use RuntimeException;

class GridGenerator
{
    private GridDefinitionLoader $gridDefinitionLoader;

    private GridColumnsGenerator $gridColumnsGenerator;

    private GridRowsGenerator $gridRowsGenerator;

    private GridFooterGenerator $gridFooterGenerator;

    private string $gridId;

    /**
     * @var iterable<array<string, mixed>|object>
     */
    private iterable $items;

    /**
     * @var array<string, mixed>
     */
    private array $arguments;

    private GridDefinition $gridDefinition;

    public function __construct(
        GridDefinitionLoader $gridDefinitionLoader,
        GridColumnsGenerator $gridColumnsGenerator,
        GridRowsGenerator $gridRowsGenerator,
        GridFooterGenerator $gridFooterGenerator
    ) {
        $this->gridDefinitionLoader = $gridDefinitionLoader;
        $this->gridColumnsGenerator = $gridColumnsGenerator;
        $this->gridRowsGenerator    = $gridRowsGenerator;
        $this->gridFooterGenerator  = $gridFooterGenerator;
    }

    /**
     * @param iterable<array<string, mixed>|object> $items
     * @param array<string, mixed>                  $arguments
     *
     * @throws Exception
     */
    public function generate(
        string $gridId,
        iterable $items,
        array $arguments
    ): array {
        $this->init($gridId, $items, $arguments);

        $this->loadGridDefinition();
        $this->validateArguments();

        return $this->buildGrid();
    }

    /**
     * @param iterable<array<string, mixed>|object> $items
     * @param array<string, mixed>                  $arguments
     */
    private function init(
        string $gridId,
        iterable $items,
        array $arguments
    ): void {
        $this->gridId    = $gridId;
        $this->items     = $items;
        $this->arguments = $arguments;
    }

    /**
     * @throws DomainException
     */
    private function loadGridDefinition(): void
    {
        $this->gridDefinition = $this->gridDefinitionLoader->load($this->gridId);
    }

    private function validateArguments(): void
    {
        foreach ($this->gridDefinition->getArguments() as $argument) {
            if (!array_key_exists($argument, $this->arguments)) {
                throw new RuntimeException("Missing grid argument '{$argument}' for grid '{$this->gridId}'.");
            }
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    private function buildGrid(): array
    {
        return [
            'columns' => $this->buildColumns(),
            'rows'    => $this->buildRows(),
            'footer'  => $this->buildFooter(),
        ];
    }

    private function buildColumns(): array
    {
        return $this->gridColumnsGenerator->generate(
            $this->gridDefinition
        );
    }

    /**
     * @return iterable<array<string, mixed>>
     *
     * @throws Exception
     */
    private function buildRows(): iterable
    {
        return $this->gridRowsGenerator->generate(
            $this->gridDefinition,
            $this->items,
            $this->arguments
        );
    }

    private function buildFooter(): array
    {
        return $this->gridFooterGenerator->generate(
            $this->gridDefinition,
            $this->items,
            $this->arguments
        );
    }
}
