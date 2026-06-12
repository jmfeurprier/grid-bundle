<?php

declare(strict_types=1);

namespace Jmf\Grid\Generation;

use Jmf\Grid\Compilation\GridDefinitionCollectionCompiler;
use Jmf\Grid\Exception\DuplicateGridException;
use Jmf\Grid\Exception\GridWithoutColumnException;
use Jmf\RenderingPreset\Exception\InvalidConfigurationException;
use Jmf\RenderingPreset\Exception\PresetNotFoundException;

readonly class GridGeneratorFactory
{
    /**
     * @param array<string, mixed> $gridConfigs
     */
    public function __construct(
        private GridDefinitionCollectionCompiler $gridDefinitionCollectionCompiler,
        private array $gridConfigs,
        private ColumnCollectionGenerator $columnCollectionGenerator,
        private RowCollectionGenerator $rowCollectionGenerator,
        private FooterGenerator $footerGenerator,
    ) {
    }

    /**
     * @throws DuplicateGridException
     * @throws GridWithoutColumnException
     * @throws InvalidConfigurationException
     * @throws PresetNotFoundException
     */
    public function create(): GridGenerator
    {
        return new GridGenerator(
            $this->gridDefinitionCollectionCompiler->compile(
                $this->gridConfigs,
            ),
            $this->columnCollectionGenerator,
            $this->rowCollectionGenerator,
            $this->footerGenerator,
        );
    }
}
