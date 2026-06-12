<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid;

use Jmf\Grid\Configuration\Grid\GridConfigurationCollectionLoader;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\Grid\Grid\Column\ColumnCollectionGenerator;
use Jmf\Grid\Grid\Footer\FooterGenerator;
use Jmf\Grid\Grid\Row\RowCollectionGenerator;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;

readonly class GridGeneratorFactory
{
    /**
     * @param array<string, mixed> $gridConfigs
     */
    public function __construct(
        private GridConfigurationCollectionLoader $gridConfigurationCollectionLoader,
        private array $gridConfigs,
        private ColumnCollectionGenerator $columnCollectionGenerator,
        private RowCollectionGenerator $rowCollectionGenerator,
        private FooterGenerator $footerGenerator,
    ) {
    }

    /**
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    public function create(): GridGenerator
    {
        return new GridGenerator(
            $this->gridConfigurationCollectionLoader->load($this->gridConfigs),
            $this->columnCollectionGenerator,
            $this->rowCollectionGenerator,
            $this->footerGenerator,
        );
    }
}
