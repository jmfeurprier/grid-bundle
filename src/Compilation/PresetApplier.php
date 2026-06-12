<?php

declare(strict_types=1);

namespace Jmf\Grid\Compilation;

use Jmf\Grid\Definition\WithPresetInterface;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
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

        return $subject->applyPreset(
            $this->presetRepository->get(
                $subject->getPresetId(),
            ),
        );
    }
}
