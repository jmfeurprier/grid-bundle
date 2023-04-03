<?php

namespace Jmf\Grid;

use Exception;
use RuntimeException;

class GridRowsGenerator
{
    private GridRowGenerator $gridRowGenerator;

    public function __construct(GridRowGenerator $gridRowGenerator)
    {
        $this->gridRowGenerator = $gridRowGenerator;
    }

    /**
     * @param array[]|object[] $items
     *
     * @throws Exception
     * @throws RuntimeException
     */
    public function generate(
        GridDefinition $gridDefinition,
        iterable $items,
        array $arguments
    ): array {
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
