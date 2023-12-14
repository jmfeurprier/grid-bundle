<?php

namespace Jmf\Grid\RenderingPreset;

use DomainException;

interface RenderingPresetRepositoryInterface
{
    /**
     * @throws DomainException
     */
    public function get(string $presetId): RenderingPreset;
}
