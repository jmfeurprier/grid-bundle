<?php

namespace Jmf\Grid\Grid;

use Exception;

class GridRowsGenerator
{
    private GridRowGenerator $gridRowGenerator;

    public function __construct(GridRowGenerator $gridRowGenerator)
    {
        $this->gridRowGenerator = $gridRowGenerator;
    }

    /**
     * @param iterable<array<string, mixed>|object> $items
     * @param array<string, mixed>                  $arguments
     *
     * @return iterable<array<string, mixed>>
     *
     * @throws Exception
     */
    public function generate(
        GridDefinition $gridDefinition,
        iterable $items,
        array $arguments
    ): iterable {
        $rowCount = count($items);
        $rowIndex = 1;
        $rows     = [];

        foreach ($items as $item) {
            $rows[] = $this->gridRowGenerator->generate(
                $gridDefinition,
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
