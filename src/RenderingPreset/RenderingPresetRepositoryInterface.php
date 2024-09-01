<?php

namespace Jmf\Grid\RenderingPreset;

use Jmf\Grid\Exception\GridException;

interface RenderingPresetRepositoryInterface
{
    /**
     * @throws GridException
     */
    public function get(string $presetId): RenderingPreset;
}
