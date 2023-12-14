<?php

namespace Jmf\Grid\RenderingPreset;

use DomainException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class CacheableRenderingPresetRepository implements RenderingPresetRepositoryInterface
{
    public function __construct(
        private RenderingPresetRepositoryInterface $renderingPresetRepository,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws DomainException
     * @throws InvalidArgumentException
     */
    public function get(string $presetId): RenderingPreset
    {
        return $this->cache->get(
            $this->getCacheKey($presetId),
            $this->getCallback($presetId),
        );
    }

    private function getCacheKey(string $presetId): string
    {
        return md5(
            serialize(
                [
                    self::class,
                    $presetId,
                ]
            )
        );
    }

    private function getCallback(string $presetId): callable
    {
        return fn(
            ItemInterface $item,
        ) => $this->renderingPresetRepository->get($presetId);
    }
}
