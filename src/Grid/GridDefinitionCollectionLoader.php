<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid;

use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Webmozart\Assert\Assert;

readonly class GridDefinitionCollectionLoader
{
    public function __construct(
        private GridDefinitionLoader $gridDefinitionLoader,
    ) {
    }

    /**
     * @param array<string, mixed> $gridConfigs
     *
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    public function load(array $gridConfigs): GridDefinitionCollection
    {
        Assert::isMap($gridConfigs);

        $gridDefinitions = [];

        foreach ($gridConfigs as $gridId => $gridConfig) {
            Assert::stringNotEmpty($gridId);
            Assert::isMap($gridConfig);

            $gridDefinitions[] = $this->gridDefinitionLoader->load($gridId, $gridConfig);
        }

        return new GridDefinitionCollection($gridDefinitions);
    }
}
