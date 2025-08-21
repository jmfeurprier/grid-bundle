<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Grid;

use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;
use Webmozart\Assert\Assert;

readonly class GridConfigurationCollectionLoader
{
    public function __construct(
        private GridConfigurationLoader $gridConfigurationLoader,
    ) {
    }

    /**
     * @param array<string, mixed> $gridConfigs
     *
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    public function load(array $gridConfigs): GridConfigurationCollection
    {
        Assert::isMap($gridConfigs);

        $gridConfigurations = [];

        foreach ($gridConfigs as $gridId => $gridConfig) {
            Assert::stringNotEmpty($gridId);
            Assert::isMap($gridConfig);

            $gridConfigurations[] = $this->gridConfigurationLoader->load($gridId, $gridConfig);
        }

        return new GridConfigurationCollection($gridConfigurations);
    }
}
