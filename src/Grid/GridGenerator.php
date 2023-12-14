<?php

namespace Jmf\Grid\Grid;

use DomainException;
use Exception;
use Jmf\Grid\Configuration\GridConfiguration;
use Jmf\Grid\Configuration\GridConfigurationLoaderInterface;
use RuntimeException;

class GridGenerator
{
    private string $gridId;

    /**
     * @var list<array<string, mixed>|object>
     */
    private array $items;

    /**
     * @var array<string, mixed>
     */
    private array $arguments;

    private GridConfiguration $gridConfiguration;

    public function __construct(
        private readonly GridConfigurationLoaderInterface $gridConfigurationLoader,
        private readonly GridColumnsGenerator $gridColumnsGenerator,
        private readonly GridRowsGenerator $gridRowsGenerator,
        private readonly GridFooterGenerator $gridFooterGenerator,
    ) {
    }

    /**
     * @param list<array<string, mixed>|object> $items
     * @param array<string, mixed>              $arguments
     *
     * @throws Exception
     */
    public function generate(
        string $gridId,
        array $items,
        array $arguments
    ): Grid {
        $this->init($gridId, $items, $arguments);

        $this->loadGridConfiguration();
        $this->validateArguments();

        return $this->buildGrid();
    }

    /**
     * @param list<array<string, mixed>|object> $items
     * @param array<string, mixed>              $arguments
     */
    private function init(
        string $gridId,
        array $items,
        array $arguments
    ): void {
        $this->gridId    = $gridId;
        $this->items     = $items;
        $this->arguments = $arguments;
    }

    /**
     * @throws DomainException
     */
    private function loadGridConfiguration(): void
    {
        $this->gridConfiguration = $this->gridConfigurationLoader->load($this->gridId);
    }

    private function validateArguments(): void
    {
        foreach ($this->gridConfiguration->getArguments() as $argument) {
            if (!array_key_exists($argument, $this->arguments)) {
                throw new RuntimeException("Missing grid argument '{$argument}' for grid '{$this->gridId}'.");
            }
        }
    }

    /**
     * @throws Exception
     */
    private function buildGrid(): Grid
    {
        return new Grid(
            $this->buildColumns(),
            $this->buildRows(),
            $this->buildFooter(),
        );
    }

    /**
     * @return GridColumn[]
     */
    private function buildColumns(): iterable
    {
        return $this->gridColumnsGenerator->generate(
            $this->gridConfiguration
        );
    }

    /**
     * @return GridRow[]
     *
     * @throws Exception
     */
    private function buildRows(): iterable
    {
        return $this->gridRowsGenerator->generate(
            $this->gridConfiguration,
            $this->items,
            $this->arguments
        );
    }

    /**
     * @throws Exception
     */
    private function buildFooter(): GridFooter
    {
        return $this->gridFooterGenerator->generate(
            $this->gridConfiguration,
            $this->items,
            $this->arguments
        );
    }
}
