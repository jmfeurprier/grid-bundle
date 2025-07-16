<?php

declare(strict_types=1);

namespace Jmf\Grid\RenderingPreset;

use Override;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Webmozart\Assert\Assert;

readonly class CacheableRenderingPresetRepository implements RenderingPresetRepositoryInterface
{
    public function __construct(
        private RenderingPresetRepositoryInterface $renderingPresetRepository,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function get(string $presetId): RenderingPreset
    {
        $renderingPreset = $this->cache->get(
            $this->getCacheKey($presetId),
            fn(
                ItemInterface $item,
            ): RenderingPreset => $this->renderingPresetRepository->get($presetId),
        );

        Assert::isInstanceOf($renderingPreset, RenderingPreset::class);

        return $renderingPreset;
    }

    /**
     * @param non-empty-string $presetId
     */
    private function getCacheKey(string $presetId): string
    {
        return md5(
            serialize(
                [
                    self::class,
                    $presetId,
                ],
            ),
        );
    }
}
