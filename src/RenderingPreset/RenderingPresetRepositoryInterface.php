<?php

declare(strict_types=1);

namespace Jmf\Grid\RenderingPreset;

use Jmf\Grid\Exception\GridException;

interface RenderingPresetRepositoryInterface
{
    /**
     * @param non-empty-string $presetId
     *
     * @throws GridException
     */
    public function get(string $presetId): RenderingPreset;
}
