<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Jmf\Grid\Configuration\GridConfiguration;
use Jmf\Grid\Exception\UnexpectedValueTypeException;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;

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
     * @throws TemplateRenderingException
     * @throws UnexpectedValueTypeException
     */
    public function generate(
        GridConfiguration $gridConfiguration,
        array $items,
        array $arguments,
    ): GridRowCollection {
        $rowCount = count($items);
        $rowIndex = 1;
        $rows     = [];

        foreach ($items as $item) {
            $rows[] = $this->gridRowGenerator->generate(
                $gridConfiguration,
                $item,
                $rowIndex,
                $rowCount,
                $arguments,
            );

            ++$rowIndex;
        }

        return new GridRowCollection($rows);
    }
}
