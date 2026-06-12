<?php

declare(strict_types=1);

namespace Jmf\Grid\Definition;

use Jmf\Grid\Exception\GridNotFoundException;
use Webmozart\Assert\Assert;

readonly class GridDefinitionCollection
{
    /**
     * @var array<non-empty-string, GridDefinition>
     */
    private array $gridDefinitions;

    /**
     * @param GridDefinition[] $gridDefinitions
     */
    public function __construct(
        iterable $gridDefinitions,
    ) {
        Assert::allIsInstanceOf($gridDefinitions, GridDefinition::class);

        $indexed = [];

        foreach ($gridDefinitions as $gridDefinition) {
            $indexed[$gridDefinition->getId()] = $gridDefinition;
        }

        $this->gridDefinitions = $indexed;
    }

    /**
     * @return GridDefinition[]
     */
    public function all(): iterable
    {
        return array_values($this->gridDefinitions);
    }

    /**
     * @throws GridNotFoundException
     */
    public function get(string $id): GridDefinition
    {
        return $this->gridDefinitions[$id] ?? throw new GridNotFoundException($id);
    }
}
