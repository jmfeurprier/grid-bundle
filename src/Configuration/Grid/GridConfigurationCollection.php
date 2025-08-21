<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Grid;

use Jmf\Grid\Exception\GridNotFoundException;
use Webmozart\Assert\Assert;

readonly class GridConfigurationCollection
{
    /**
     * @var array<non-empty-string, GridConfiguration>
     */
    private array $gridConfigurations;

    /**
     * @param GridConfiguration[] $gridConfigurations
     */
    public function __construct(
        iterable $gridConfigurations,
    ) {
        Assert::allIsInstanceOf($gridConfigurations, GridConfiguration::class);

        $indexed = [];

        foreach ($gridConfigurations as $gridConfiguration) {
            $indexed[$gridConfiguration->getId()] = $gridConfiguration;
        }

        $this->gridConfigurations = $indexed;
    }

    /**
     * @return GridConfiguration[]
     */
    public function all(): iterable
    {
        return array_values($this->gridConfigurations);
    }

    /**
     * @throws GridNotFoundException
     */
    public function get(string $id): GridConfiguration
    {
        return $this->gridConfigurations[$id] ?? throw new GridNotFoundException($id);
    }
}
