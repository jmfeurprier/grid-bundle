<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Jmf\Grid\Grid\Row\AttributesLoader;
use Jmf\Grid\Grid\KeyValueCollection;
use Webmozart\Assert\Assert;

readonly class RowDefinitionLoader
{
    public function __construct(
        private AttributesLoader $attributesLoader,
    ) {
    }

    /**
     * @param array<string, mixed> $rowConfig
     */
    public function load(array $rowConfig): RowDefinition
    {
        Assert::isMap($rowConfig);

        return new RowDefinition(
            $this->getLink($rowConfig),
            $this->getVariables($rowConfig),
            $this->getAttributes($rowConfig),
        );
    }

    /**
     * @param array<string, mixed> $rowConfig
     */
    private function getLink(array $rowConfig): ?string
    {
        $link = $rowConfig['link'] ?? null;

        Assert::nullOrString($link);

        return $link;
    }

    /**
     * @param array<string, mixed> $rowConfig
     */
    private function getVariables(array $rowConfig): KeyValueCollection
    {
        if (!isset($rowConfig['variables'])) {
            return KeyValueCollection::createEmpty();
        }

        $variablesConfig = $rowConfig['variables'];

        Assert::isMap($variablesConfig);

        return new KeyValueCollection($variablesConfig);
    }

    /**
     * @param array<string, mixed> $rowConfig
     */
    private function getAttributes(array $rowConfig): KeyValueCollection
    {
        return $this->attributesLoader->load($rowConfig);
    }
}
