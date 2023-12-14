<?php

namespace Jmf\Grid\Grid;

use Exception;
use Jmf\Grid\Configuration\GridConfiguration;

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
     * @throws Exception
     */
    public function generate(
        GridConfiguration $gridConfiguration,
        array $items,
        array $arguments
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
