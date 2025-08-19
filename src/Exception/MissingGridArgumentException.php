<?php

declare(strict_types=1);

namespace Jmf\Grid\Exception;

class MissingGridArgumentException extends GridException
{
    public function __construct(
        private readonly string $gridId,
        private readonly string $argument,
    ) {
        parent::__construct(
            sprintf(
                "Missing grid argument '%s' for grid '%s'.",
                $argument,
                $gridId,
            ),
        );
    }

    public function getGridId(): string
    {
        return $this->gridId;
    }

    public function getArgument(): string
    {
        return $this->argument;
    }
}
