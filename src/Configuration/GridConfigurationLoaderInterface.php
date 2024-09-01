<?php

namespace Jmf\Grid\Configuration;

use Jmf\Grid\Exception\GridException;

interface GridConfigurationLoaderInterface
{
    /**
     * @throws GridException
     */
    public function load(string $gridId): GridConfiguration;
}
