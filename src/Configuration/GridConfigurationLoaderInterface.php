<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration;

use Jmf\Grid\Exception\GridException;

interface GridConfigurationLoaderInterface
{
    /**
     * @param non-empty-string $gridId
     *
     * @throws GridException
     */
    public function load(string $gridId): GridConfiguration;
}
