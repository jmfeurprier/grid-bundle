<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration;

use Jmf\Grid\Exception\DuplicateGridException;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

/**
 * Owns the `grids` side of the bundle configuration: assembles the final map from the per-grid files
 * discovered under the configured `paths` (filename = grid id) merged with the inline `grids`, and
 * registers a cache-invalidation resource per directory. A grid id defined more than once (across
 * files, or against an inline entry) is a configuration error.
 */
readonly class GridDefinitionFileLoader
{
    /**
     * @param array<string, mixed> $config         resolved `jmf_grid` config (`paths` + `grids`)
     * @param string               $extensionAlias used to derive the default path
     *
     * @return array<string, array<string, mixed>> grid configs keyed by id
     *
     * @throws DuplicateGridException
     */
    public function load(
        array $config,
        ContainerBuilder $container,
        string $extensionAlias,
    ): array {
        $directories = $this->resolveDirectories($config, $container, $extensionAlias);

        $this->registerResources($directories, $container);

        $gridsFromPaths = $this->loadFromPaths($directories);

        /** @var array<string, array<string, mixed>> $inlineGrids */
        $inlineGrids = $config['grids'];
        Assert::isArray($inlineGrids);

        $duplicates = array_intersect_key($gridsFromPaths, $inlineGrids);

        if ([] !== $duplicates) {
            $duplicateIds = array_keys($duplicates);
            Assert::allStringNotEmpty($duplicateIds);

            throw new DuplicateGridException($duplicateIds);
        }

        return $gridsFromPaths + $inlineGrids;
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return list<string> resolved (absolute) directories
     */
    private function resolveDirectories(
        array $config,
        ContainerBuilder $container,
        string $extensionAlias,
    ): array {
        $paths = $config['paths'];
        Assert::isArray($paths);

        if ([] === $paths) {
            // Default: <config-dir>/packages/<extension alias>, e.g. config/packages/jmf_grid.
            // `.kernel.config_dir` is the build-time materialization of Kernel::getConfigDir(), the
            // only handle a bundle extension has to it; the alias keeps the segment rename-safe.
            $paths = ['%.kernel.config_dir%/packages/' . $extensionAlias];
        }

        $directories = [];

        foreach ($paths as $path) {
            Assert::string($path);

            $directory = $container->getParameterBag()->resolveValue($path);
            Assert::string($directory);

            $directories[] = $directory;
        }

        return $directories;
    }

    /**
     * @param list<string> $directories
     */
    private function registerResources(
        array $directories,
        ContainerBuilder $container,
    ): void {
        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                // DirectoryResource is mtime-based + recursive, so the compiled container is
                // rebuilt when a file in the directory is added, removed or edited.
                $container->addResource(new DirectoryResource($directory, '/\.yaml$/'));
            }
        }
    }

    /**
     * @param list<string> $directories
     *
     * @return array<string, array<string, mixed>>
     *
     * @throws DuplicateGridException
     */
    private function loadFromPaths(
        array $directories,
    ): array {
        $grids = [];

        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            foreach ((new Finder())->files()->in($directory)->name('*.yaml')->sortByName() as $file) {
                $relativeName = substr($file->getRelativePathname(), 0, -strlen('.yaml'));
                $gridId       = str_replace('/', '.', $relativeName);
                Assert::stringNotEmpty($gridId);

                if (isset($grids[$gridId])) {
                    throw new DuplicateGridException([$gridId]);
                }

                // PARSE_CONSTANT so files may use `!php/const ...`, matching what Symfony's own
                // config loader enables for inline config.
                /** @var array<string, mixed> $parsed */
                $parsed = Yaml::parseFile($file->getRealPath(), Yaml::PARSE_CONSTANT);

                $grids[$gridId] = is_array($parsed) ? $parsed : [];
            }
        }

        return $grids;
    }
}
