<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Grid;

use Jmf\Grid\Exception\GridNotFoundException;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;

interface GridConfigurationLoaderInterface
{
    /**
     * @param non-empty-string $gridId
     *
     * @throws GridNotFoundException
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    public function load(string $gridId): GridConfiguration;
}
