<?php

declare(strict_types=1);

namespace Jmf\Grid\Compilation;

use Jmf\Grid\Definition\GridDefinitionCollection;
use Jmf\Grid\Exception\DuplicateGridException;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Webmozart\Assert\Assert;

readonly class GridDefinitionCollectionCompiler
{
    public function __construct(
        private GridDefinitionCompiler $gridDefinitionCompiler,
    ) {
    }

    /**
     * @param array<string, mixed> $gridConfigs
     *
     * @throws DuplicateGridException
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    public function compile(array $gridConfigs): GridDefinitionCollection
    {
        Assert::isMap($gridConfigs);

        $gridDefinitions = [];

        foreach ($gridConfigs as $gridId => $gridConfig) {
            Assert::stringNotEmpty($gridId);
            Assert::isMap($gridConfig);

            $gridDefinitions[] = $this->gridDefinitionCompiler->compile(
                $gridId,
                $gridConfig,
            );
        }

        return new GridDefinitionCollection($gridDefinitions);
    }
}
