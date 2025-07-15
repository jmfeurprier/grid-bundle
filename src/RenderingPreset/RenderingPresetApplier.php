<?php

declare(strict_types=1);

namespace Jmf\Grid\RenderingPreset;

use Jmf\Grid\Exception\GridException;

readonly class RenderingPresetApplier
{
    public function __construct(
        private RenderingPresetRepositoryInterface $renderingPresetRepository,
    ) {
    }

    /**
     * @psalm-template T of WithRenderingPresetInterface
     *
     * @psalm-param T $subject
     *
     * @psalm-return T
     *
     * @throws GridException
     */
    public function apply(WithRenderingPresetInterface $subject): WithRenderingPresetInterface
    {
        if (null === $subject->getPresetId()) {
            return $subject;
        }

        $renderingPreset = $this->getRenderingPreset($subject->getPresetId());

        return $this->apply(
            $subject->applyPreset($renderingPreset),
        );
    }

    /**
     * @param non-empty-string $presetId
     *
     * @throws GridException
     */
    private function getRenderingPreset(string $presetId): RenderingPreset
    {
        return $this->renderingPresetRepository->get($presetId);
    }
}
