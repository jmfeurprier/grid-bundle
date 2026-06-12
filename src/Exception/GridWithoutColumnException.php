<?php

declare(strict_types=1);

namespace Jmf\Grid\Exception;

class GridWithoutColumnException extends GridException
{
    /**
     * @param non-empty-string $gridId
     */
    public function __construct(string $gridId)
    {
        parent::__construct(sprintf('Grid "%s" has no column defined.', $gridId));
    }
}
