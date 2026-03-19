<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Row;

use Jmf\Grid\Configuration\KeyValueCollection;
use Webmozart\Assert\Assert;

readonly class RowConfigurationLoader
{
    /**
     * @param array<string, mixed> $rowConfig
     */
    public function load(array $rowConfig): RowConfiguration
    {
        Assert::isMap($rowConfig);

        return new RowConfiguration(
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
        if (!isset($rowConfig['attributes'])) {
            return KeyValueCollection::createEmpty();
        }

        $attributesConfig = $rowConfig['attributes'];

        Assert::isMap($attributesConfig);

        return new KeyValueCollection($attributesConfig);
    }
}
