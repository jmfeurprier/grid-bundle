<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Jmf\Grid\Grid\KeyValueCollection;

readonly class RowDefinition
{
    public static function createEmpty(): self
    {
        return new self(
            null,
            KeyValueCollection::createEmpty(),
            KeyValueCollection::createEmpty(),
        );
    }

    public function __construct(
        private ?string $link,
        private KeyValueCollection $variables,
        private KeyValueCollection $attributes,
    ) {
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function getVariables(): KeyValueCollection
    {
        return $this->variables;
    }

    public function getAttributes(): KeyValueCollection
    {
        return $this->attributes;
    }
}
