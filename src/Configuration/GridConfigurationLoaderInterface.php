<?php

namespace Jmf\Grid\Configuration;

use DomainException;

interface GridConfigurationLoaderInterface
{
    /**
     * @throws DomainException
     */
    public function load(string $gridId): GridConfiguration;
}
