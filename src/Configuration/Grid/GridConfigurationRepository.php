<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Grid;

use Override;

readonly class GridConfigurationRepository implements GridConfigurationRepositoryInterface
{
    /**
     * @param array<string, mixed> $gridConfigs
     */
    public function __construct(
        private GridConfigurationCollectionLoader $gridConfigurationCollectionLoader,
        private array $gridConfigs,
    ) {
    }

    #[Override]
    public function getCollection(): GridConfigurationCollection
    {
        return $this->gridConfigurationCollectionLoader->load($this->gridConfigs);
    }
}
