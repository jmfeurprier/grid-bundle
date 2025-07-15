<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration;

use Override;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Webmozart\Assert\Assert;

readonly class CacheableGridConfigurationLoader implements GridConfigurationLoaderInterface
{
    public function __construct(
        private GridConfigurationLoaderInterface $gridConfigurationLoader,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function load(string $gridId): GridConfiguration
    {
        $gridConfiguration = $this->cache->get(
            $this->getCacheKey($gridId),
            $this->getCallback($gridId),
        );

        Assert::isInstanceOf($gridConfiguration, GridConfiguration::class);

        return $gridConfiguration;
    }

    /**
     * @param non-empty-string $gridId
     *
     * @return non-empty-string
     */
    private function getCacheKey(string $gridId): string
    {
        return md5(
            serialize(
                [
                    self::class,
                    $gridId,
                ],
            ),
        );
    }

    /**
     * @param non-empty-string $gridId
     */
    private function getCallback(string $gridId): callable
    {
        return fn(
            ItemInterface $item,
        ): GridConfiguration => $this->gridConfigurationLoader->load($gridId);
    }
}
