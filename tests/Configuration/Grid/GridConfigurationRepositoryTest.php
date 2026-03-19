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
        $gridConfigs                 = ['someKey' => 'someValue'];
        $gridConfigurationCollection = new GridConfigurationCollection([]);

        $gridConfigurationCollectionLoader = $this->createMock(GridConfigurationCollectionLoader::class);
        $gridConfigurationCollectionLoader->expects($this->once())
            ->method('load')
            ->with($gridConfigs)
            ->willReturn($gridConfigurationCollection)
        ;

        $gridConfigurationRepository = new GridConfigurationRepository(
            $gridConfigurationCollectionLoader,
            $gridConfigs,
        );

        $result = $gridConfigurationRepository->getCollection();

        self::assertSame($gridConfigurationCollection, $result);
    }

    public function testGetCollectionCallsLoaderEachTime(): void
    {
        $gridConfigurationCollection = new GridConfigurationCollection([]);

        $gridConfigurationCollectionLoader = $this->createMock(GridConfigurationCollectionLoader::class);
        $gridConfigurationCollectionLoader->expects($this->exactly(2))
            ->method('load')
            ->willReturn($gridConfigurationCollection)
        ;

        $gridConfigurationRepository = new GridConfigurationRepository(
            $gridConfigurationCollectionLoader,
            [],
        );

        $gridConfigurationRepository->getCollection();
        $gridConfigurationRepository->getCollection();
    }
}
