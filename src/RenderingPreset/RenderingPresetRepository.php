<?php

namespace Jmf\Grid\RenderingPreset;

use DomainException;
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
     * @throws DomainException
     */
    #[Override]
    public function get(string $presetId): RenderingPreset
    {
        return $this->renderingPresetCollectionLoader->load($this->renderingPresetConfigs)->get($presetId);
    }
}
