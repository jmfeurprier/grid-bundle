<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Jmf\Grid\Grid\GridDefinition;
use Jmf\Grid\Exception\UnexpectedValueTypeException;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;

readonly class RowCollectionGenerator
{
    public function __construct(
        private RowGenerator $gridRowGenerator,
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
        GridDefinition $gridDefinition,
        array $items,
        array $arguments,
    ): RowCollection {
        $rowCount = count($items);
        $rowIndex = 1;
        $rows     = [];

        foreach ($items as $item) {
            $rows[] = $this->gridRowGenerator->generate(
                $gridDefinition,
                $item,
                $rowIndex,
                $rowCount,
                $arguments,
            );

            ++$rowIndex;
        }

        return new RowCollection($rows);
    }
}
