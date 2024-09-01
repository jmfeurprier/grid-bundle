<?php

namespace Jmf\Grid\Grid;

use Jmf\Grid\Configuration\GridConfiguration;
use Jmf\Grid\Exception\GridException;

readonly class GridRowsGenerator
{
    public function __construct(
        private GridRowGenerator $gridRowGenerator,
    ) {
    }

    /**
     * @param list<array<string, mixed>|object> $items
     * @param array<string, mixed>              $arguments
     *
     * @return GridRow[]
     *
     * @throws GridException
     */
    public function generate(
        GridConfiguration $gridConfiguration,
        array $items,
        array $arguments,
    ): iterable {
        $rowCount = count($items);
        $rowIndex = 1;
        $rows     = [];

        foreach ($items as $item) {
            $rows[] = $this->gridRowGenerator->generate(
                $gridConfiguration,
                $item,
                $rowIndex,
                $rowCount,
                $arguments
            );

            ++$rowIndex;
        }

        return $rows;
    }
}
