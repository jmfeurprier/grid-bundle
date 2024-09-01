<?php

namespace Jmf\Grid\RenderingPreset;

use Jmf\Grid\Exception\GridException;
use Override;

readonly class RenderingPresetRepository implements RenderingPresetRepositoryInterface
{
    /**
     * @param array<string, mixed> $renderingPresetConfigs
     */
    public function __construct(
        private RenderingPresetCollectionLoader $renderingPresetCollectionLoader,
        private array $renderingPresetConfigs,
    ) {
    }

    /**
     * @throws GridException
     */
    #[Override]
    public function get(string $presetId): RenderingPreset
    {
        return $this->renderingPresetCollectionLoader->load($this->renderingPresetConfigs)->get($presetId);
    }
}
