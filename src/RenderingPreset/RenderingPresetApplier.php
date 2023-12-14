<?php

namespace Jmf\Grid\RenderingPreset;

class RenderingPresetApplier
{
    public function __construct(
        private readonly RenderingPresetRepositoryInterface $renderingPresetRepository,
    ) {
    }

    /**
     * @template T of WithRenderingPresetInterface
     *
     * @param T $subject
     *
     * @return T
     */
    public function apply(WithRenderingPresetInterface $subject): WithRenderingPresetInterface
    {
        if (null === $subject->getPreset()) {
            return $subject;
        }

        $renderingPreset = $this->getRenderingPreset($subject->getPreset());

        return $this->apply(
            $subject->applyPreset($renderingPreset)
        );
    }

    private function getRenderingPreset(string $presetId): RenderingPreset
    {
        return $this->renderingPresetRepository->get($presetId);
    }
}
