<?php

declare(strict_types=1);

namespace Jmf\Grid\Preset;

use Jmf\Grid\Exception\GridException;
use Jmf\PresetRendering\Exception\PresetRenderingException;
use Jmf\PresetRendering\Preset\Preset;
use Jmf\PresetRendering\Preset\PresetRepositoryInterface;
use Throwable;

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
     * @throws GridException
     */
    public function apply(WithPresetInterface $subject): WithPresetInterface
    {
        if (null === $subject->getPresetId()) {
            return $subject;
        }

        $preset = $this->getPreset($subject->getPresetId());

        return $this->apply(
            $subject->applyPreset($preset),
        );
    }

    /**
     * @param non-empty-string $presetId
     *
     * @throws GridException
     */
    private function getPreset(string $presetId): Preset
    {
        try {
            return $this->presetRepository->get($presetId);
        } catch (Throwable $e) {
            throw new GridException(
                message:  'Failed retrieving preset.',
                previous: $e,
            );
        }
    }
}
