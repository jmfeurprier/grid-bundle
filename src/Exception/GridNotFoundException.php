<?php

declare(strict_types=1);

namespace Jmf\Grid\Exception;

class GridNotFoundException extends GridException
{
    public function __construct(
        private readonly string $gridId,
    ) {
        parent::__construct(
            sprintf(
                "Grid with Id '%s' is not defined.",
                $gridId,
            ),
        );
    }

    public function getGridId(): string
    {
        return $this->gridId;
    }
}
