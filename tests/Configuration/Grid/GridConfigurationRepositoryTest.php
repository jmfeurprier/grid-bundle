<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration\Grid;

use Jmf\Grid\Configuration\Grid\GridConfigurationCollection;
use Jmf\Grid\Configuration\Grid\GridConfigurationCollectionLoader;
use Jmf\Grid\Configuration\Grid\GridConfigurationRepository;
use PHPUnit\Framework\TestCase;

final class GridConfigurationRepositoryTest extends TestCase
{
    public function testGetCollectionDelegatestoLoader(): void
    {
        $gridConfigs = ['someKey' => 'someValue'];
        $collection  = new GridConfigurationCollection([]);

        $loader = $this->createMock(GridConfigurationCollectionLoader::class);
        $loader->expects($this->once())
            ->method('load')
            ->with($gridConfigs)
            ->willReturn($collection)
        ;

        $repository = new GridConfigurationRepository($loader, $gridConfigs);

        $result = $repository->getCollection();

        self::assertSame($collection, $result);
    }

    public function testGetCollectionCallsLoaderEachTime(): void
    {
        $collection = new GridConfigurationCollection([]);

        $loader = $this->createMock(GridConfigurationCollectionLoader::class);
        $loader->expects($this->exactly(2))
            ->method('load')
            ->willReturn($collection)
        ;

        $repository = new GridConfigurationRepository($loader, []);

        $repository->getCollection();
        $repository->getCollection();
    }
}
