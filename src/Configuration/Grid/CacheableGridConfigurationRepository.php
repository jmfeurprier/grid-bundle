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
    /**
     * @param array<string, mixed> $gridConfigs
     */
    public function __construct(
        private GridConfigurationRepositoryInterface $wrapped,
        private array $gridConfigs,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Override]
    public function getCollection(): GridConfigurationCollection
    {
        $gridConfigurationCollection = $this->cache->get(
            $this->getCacheKey(),
            fn(
                ItemInterface $item,
            ): GridConfigurationCollection => $this->wrapped->getCollection(),
        );

        Assert::isInstanceOf($gridConfigurationCollection, GridConfigurationCollection::class);

        return $gridConfigurationCollection;
    }

    /**
     * @return non-empty-string
     */
    private function getCacheKey(): string
    {
        return md5(
            serialize(
                [
                    self::class,
                    $this->gridConfigs,
                ],
            ),
        );
    }
}
