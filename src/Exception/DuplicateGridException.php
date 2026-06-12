<?php

declare(strict_types=1);

namespace Jmf\Grid\Exception;

class DuplicateGridException extends GridException
{
    /**
     * @param non-empty-list<non-empty-string> $gridIds
     */
    public function __construct(
        private readonly array $gridIds,
    ) {
        parent::__construct(
            sprintf(
                'Duplicate grid configuration for %s.',
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
