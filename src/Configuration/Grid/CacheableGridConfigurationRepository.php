<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Grid;

use Override;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Webmozart\Assert\Assert;

readonly class CacheableGridConfigurationRepository implements GridConfigurationRepositoryInterface
{
    private string $cacheKey;

    /**
     * @param array<string, mixed> $gridConfigs
     */
    public function __construct(
        private GridConfigurationRepositoryInterface $wrapped,
        private CacheInterface $cache,
        array $gridConfigs,
    ) {
        $this->cacheKey = md5(
            serialize(
                [
                    self::class,
                    $gridConfigs,
                ],
            ),
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function getCollection(): GridConfigurationCollection
    {
        $gridConfigurationCollection = $this->cache->get(
            $this->cacheKey,
            fn(
                ItemInterface $item,
            ): GridConfigurationCollection => $this->wrapped->getCollection(),
        );

        Assert::isInstanceOf($gridConfigurationCollection, GridConfigurationCollection::class);

        return $gridConfigurationCollection;
    }
}
