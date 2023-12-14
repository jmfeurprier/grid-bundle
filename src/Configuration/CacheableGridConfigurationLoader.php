<?php

namespace Jmf\Grid\Configuration;

use DomainException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class CacheableGridConfigurationLoader implements GridConfigurationLoaderInterface
{
    public function __construct(
        private GridConfigurationLoaderInterface $gridConfigurationLoader,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws DomainException
     */
    public function load(string $gridId): GridConfiguration
    {
        return $this->cache->get(
            $this->getCacheKey($gridId),
            $this->getCallback($gridId),
        );
    }

    private function getCacheKey(string $gridId): string
    {
        return md5(
            serialize(
                [
                    self::class,
                    $gridId,
                ]
            )
        );
    }

    private function getCallback(string $gridId): callable
    {
        return fn(
            ItemInterface $item,
        ) => $this->gridConfigurationLoader->load($gridId);
    }
}
