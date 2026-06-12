<?php

declare(strict_types=1);

namespace Jmf\Grid\Generation;

use Jmf\Grid\Definition\GridDefinition;
use Jmf\Grid\Exception\UnexpectedValueTypeException;
use Jmf\Grid\Model\RowCollection;
use Jmf\TemplateRendering\Exception\TemplateRenderingException;

readonly class RowCollectionGenerator
{
    public function __construct(
        private RowGenerator $rowGenerator,
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
            $rows[] = $this->rowGenerator->generate(
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
