<?php

declare(strict_types=1);

namespace Jmf\Grid\Tests\Configuration;

use Jmf\Grid\Configuration\GridDefinitionFileLoader;
use Jmf\Grid\Exception\DuplicateGridException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class GridDefinitionFileLoaderTest extends TestCase
{
    private const string FIXTURES_DIR = __DIR__ . '/../Fixtures/grids';

    public function testMapsFilenameToGridId(): void
    {
        $containerBuilder = new ContainerBuilder();

        $grids = (new GridDefinitionFileLoader())->load(
            $this->config([self::FIXTURES_DIR]),
            $containerBuilder,
            'jmf_grid',
        );

        self::assertSame(
            [
                'countries' => ['columns' => [['label' => 'Name', 'source' => 'name']]],
            ],
            $grids,
        );

        // The directory is tracked so the compiled container rebuilds on file changes.
        $directoryResources = array_filter(
            $containerBuilder->getResources(),
            static fn (object $resource): bool => $resource instanceof DirectoryResource,
        );
        self::assertNotEmpty($directoryResources);
    }

    public function testInlineGridsAreMerged(): void
    {
        $grids = (new GridDefinitionFileLoader())->load(
            $this->config([self::FIXTURES_DIR], ['users' => ['columns' => []]]),
            new ContainerBuilder(),
            'jmf_grid',
        );

        self::assertArrayHasKey('countries', $grids);
        self::assertArrayHasKey('users', $grids);
    }

    public function testMissingDirectoryYieldsOnlyInline(): void
    {
        $grids = (new GridDefinitionFileLoader())->load(
            $this->config([__DIR__ . '/does-not-exist']),
            new ContainerBuilder(),
            'jmf_grid',
        );

        self::assertSame([], $grids);
    }

    public function testDuplicateBetweenFileAndInlineThrows(): void
    {
        $this->expectException(DuplicateGridException::class);

        (new GridDefinitionFileLoader())->load(
            $this->config([self::FIXTURES_DIR], ['countries' => ['columns' => []]]),
            new ContainerBuilder(),
            'jmf_grid',
        );
    }

    public function testDuplicateGridIdAcrossPathsThrows(): void
    {
        $this->expectException(DuplicateGridException::class);

        (new GridDefinitionFileLoader())->load(
            $this->config([self::FIXTURES_DIR, self::FIXTURES_DIR]),
            new ContainerBuilder(),
            'jmf_grid',
        );
    }

    /**
     * @param list<string>         $paths
     * @param array<string, mixed> $grids
     *
     * @return array<string, mixed>
     */
    private function config(
        array $paths,
        array $grids = [],
    ): array {
        return [
            'paths' => $paths,
            'grids' => $grids,
        ];
    }
}
