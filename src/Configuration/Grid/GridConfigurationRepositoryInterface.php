<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Grid;

use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;

interface GridConfigurationRepositoryInterface
{
    /**
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    public function getCollection(): GridConfigurationCollection;
}
