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
        $collection = new GridConfigurationCollection([]);

        $wrapped = $this->createStub(GridConfigurationRepositoryInterface::class);
        $wrapped->method('getCollection')->willReturn($collection);

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->once())
            ->method('get')
            ->willReturnCallback(function (string $key, callable $callback): mixed {
                $item = $this->createStub(ItemInterface::class);

                return $callback($item);
            });

        $repository = new CacheableGridConfigurationRepository($wrapped, [], $cache);

        $result = $repository->getCollection();

        self::assertSame($collection, $result);
    }

    public function testGetCollectionUsesWrappedRepositoryInsideCallback(): void
    {
        $collection = new GridConfigurationCollection([]);

        $wrapped = $this->createMock(GridConfigurationRepositoryInterface::class);
        $wrapped->expects($this->once())
            ->method('getCollection')
            ->willReturn($collection);

        $cache = $this->createStub(CacheInterface::class);
        $cache->method('get')
            ->willReturnCallback(function (string $key, callable $callback): mixed {
                $item = $this->createStub(ItemInterface::class);

                return $callback($item);
            });

        $repository = new CacheableGridConfigurationRepository($wrapped, [], $cache);
        $repository->getCollection();
    }

    public function testGetCollectionUsesDifferentCacheKeyForDifferentConfigs(): void
    {
        $collection = new GridConfigurationCollection([]);

        $wrapped = $this->createStub(GridConfigurationRepositoryInterface::class);
        $wrapped->method('getCollection')->willReturn($collection);

        $usedKeys = [];

        $cache = $this->createStub(CacheInterface::class);
        $cache->method('get')
            ->willReturnCallback(function (string $key, callable $callback) use (&$usedKeys): mixed {
                $usedKeys[] = $key;
                $item       = $this->createStub(ItemInterface::class);

                return $callback($item);
            });

        $repoA = new CacheableGridConfigurationRepository($wrapped, ['configA' => 'valueA'], $cache);
        $repoB = new CacheableGridConfigurationRepository($wrapped, ['configB' => 'valueB'], $cache);

        $repoA->getCollection();
        $repoB->getCollection();

        self::assertCount(2, $usedKeys);
        self::assertNotSame($usedKeys[0], $usedKeys[1]);
    }
}
