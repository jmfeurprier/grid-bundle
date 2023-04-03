<?php

namespace Jmf\Grid;

use DomainException;
use RuntimeException;

class GridDefinitionLoader
{
    private array $entityRenderingPresets;

    private string $gridId;

    private array $gridDefinitions;

    private array $gridDefinition;

    public function __construct(
        array $gridDefinitions,
        array $entityRenderingPresets
    ) {
        $this->gridDefinitions        = $gridDefinitions;
        $this->entityRenderingPresets = $entityRenderingPresets;
    }

    /**
     * @throws RuntimeException
     */
    public function load(string $gridId): GridDefinition
    {
        $this->init($gridId);

        $this->loadGridDefinition();
        $this->applyPresets();

        return new GridDefinition($this->gridDefinition);
    }

    private function init(string $gridId): void
    {
        $this->gridId = $gridId;
    }

    /**
     * @throws DomainException
     */
    private function loadGridDefinition(): void
    {
        if (!array_key_exists($this->gridId, $this->gridDefinitions)) {
            throw new DomainException("No grid definition found with name '{$this->gridId}'.");
        }

        $this->gridDefinition = $this->gridDefinitions[$this->gridId];
    }

    private function applyPresets(): void
    {
        foreach ($this->gridDefinition['columns'] as $columnId => $columnDefinition) {
            $this->gridDefinition['columns'][$columnId] = $this->applyPresetToColumnDefinition($columnDefinition);
        }
    }

    /**
     * @throws RuntimeException
     */
    private function applyPresetToColumnDefinition(array $columnDefinition): array
    {
        if (empty($columnDefinition['preset'])) {
            return $columnDefinition;
        }

        $presetId = $columnDefinition['preset'];
        unset($columnDefinition['preset']);

        $newColumnParameters = array_merge(
            $this->getPreset($presetId),
            $columnDefinition
        );

        return $this->applyPresetToColumnDefinition($newColumnParameters);
    }

    /**
     * @throws RuntimeException
     */
    private function getPreset(string $presetId): array
    {
        if (isset($this->entityRenderingPresets[$presetId])) {
            return $this->entityRenderingPresets[$presetId];
        }

        throw new RuntimeException("Table column preset '{$presetId}' not defined.");
    }
}
