<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Preset;

use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Jmf\RenderingPreset\Preset\Preset;
use Jmf\RenderingPreset\Preset\PresetRepositoryInterface;

readonly class PresetApplier
{
    public function __construct(
        private PresetRepositoryInterface $presetRepository,
    ) {
    }

    /**
     * @psalm-template T of WithPresetInterface
     *
     * @psalm-param T $subject
     *
     * @psalm-return T
     *
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    public function apply(WithPresetInterface $subject): WithPresetInterface
    {
        if (null === $subject->getPresetId()) {
            return $subject;
        }

        $preset = $this->getPreset($subject->getPresetId());

        return $subject->applyPreset($preset);
    }

    /**
     * @param non-empty-string $presetId
     *
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    private function getPreset(string $presetId): Preset
    {
        return $this->presetRepository->get($presetId);
    }
}
