<?php

declare(strict_types=1);

namespace Jmf\Grid\Exception;

class DuplicateGridException extends GridException
{
    /**
     * @param non-empty-list<string> $gridIds
     */
    public function __construct(
        private readonly array $gridIds,
    ) {
        parent::__construct(
            sprintf(
                'Duplicate grid configuration for %s: defined both inline under "grids" and via "paths".',
                implode(', ', $this->gridIds),
            ),
        );
    }

    /**
     * @return non-empty-list<string>
     */
    public function getGridIds(): array
    {
        return $this->gridIds;
    }
}
