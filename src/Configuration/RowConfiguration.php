<?php

namespace Jmf\Grid\Configuration;

readonly class RowConfiguration
{
    public static function createEmpty(): self
    {
        return new self(
            null,
            KeyValueCollection::createEmpty(),
        );
    }

    public function __construct(
        private ?string $link,
        private KeyValueCollection $variables,
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
}
