<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Grid;

use Jmf\Grid\Configuration\Grid\CacheableGridConfigurationRepository;
use Jmf\Grid\Configuration\Grid\GridConfigurationCollection;
use Jmf\Grid\Configuration\Grid\GridConfigurationRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CacheableGridConfigurationRepositoryTest extends TestCase
{
    public function testGetCollectionReturnsCachedResult(): void
    {
        $gridConfigurationCollection = new GridConfigurationCollection([]);

        $gridConfigurationRepository = $this->createStub(GridConfigurationRepositoryInterface::class);
        $gridConfigurationRepository->method('getCollection')->willReturn($gridConfigurationCollection);

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(
                function (
                    string $key,
                    callable $callback,
                ): mixed {
                    $item = $this->createStub(ItemInterface::class);

                    return $callback($item);
                },
            )
        ;

        $cacheableGridConfigurationRepository =
            new CacheableGridConfigurationRepository($gridConfigurationRepository, [], $cache);

        $result = $cacheableGridConfigurationRepository->getCollection();

        self::assertSame($gridConfigurationCollection, $result);
    }

    public function testGetCollectionUsesWrappedRepositoryInsideCallback(): void
    {
        $gridConfigurationCollection = new GridConfigurationCollection([]);

        $gridConfigurationRepository = $this->createMock(GridConfigurationRepositoryInterface::class);
        $gridConfigurationRepository->expects($this->once())
            ->method('getCollection')
            ->willReturn($gridConfigurationCollection)
        ;

        $cache = $this->createStub(CacheInterface::class);
        $cache->method('get')
            ->willReturnCallback(
                function (
                    string $key,
                    callable $callback,
                ): mixed {
                    $item = $this->createStub(ItemInterface::class);

                    return $callback($item);
                },
            )
        ;

        $cacheableGridConfigurationRepository =
            new CacheableGridConfigurationRepository($gridConfigurationRepository, [], $cache);

        $cacheableGridConfigurationRepository->getCollection();
    }

    public function testGetCollectionUsesDifferentCacheKeyForDifferentConfigs(): void
    {
        $gridConfigurationCollection = new GridConfigurationCollection([]);

        $gridConfigurationRepository = $this->createStub(GridConfigurationRepositoryInterface::class);
        $gridConfigurationRepository->method('getCollection')->willReturn($gridConfigurationCollection);

        $usedKeys = [];

        $cache = $this->createStub(CacheInterface::class);
        $cache->method('get')
            ->willReturnCallback(
                function (
                    string $key,
                    callable $callback,
                ) use
                (
                    &$usedKeys,
                ): mixed {
                    $usedKeys[] = $key;
                    $item       = $this->createStub(ItemInterface::class);

                    return $callback($item);
                },
            )
        ;

        $cacheableGridConfigurationRepositoryPrimary   = new CacheableGridConfigurationRepository(
            $gridConfigurationRepository,
            ['configA' => 'valueA'],
            $cache,
        );
        $cacheableGridConfigurationRepositorySecondary = new CacheableGridConfigurationRepository(
            $gridConfigurationRepository,
            ['configB' => 'valueB'],
            $cache,
        );

        $cacheableGridConfigurationRepositoryPrimary->getCollection();
        $cacheableGridConfigurationRepositorySecondary->getCollection();

        self::assertCount(2, $usedKeys);
        self::assertNotSame($usedKeys[0], $usedKeys[1]);
    }
}
