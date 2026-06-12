<?php

declare(strict_types=1);

namespace Jmf\Grid\Exception;

class UnexpectedValueTypeException extends GridException
{
    // @todo Add context (grid Id, etc).
    public function __construct(string $valueType)
    {
        parent::__construct(sprintf('Unexpected cell value type "%s".', $valueType));
    }
}
