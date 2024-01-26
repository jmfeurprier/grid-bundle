<?php

namespace Jmf\Grid\Configuration;

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

        Assert::isArray($variablesConfig);

        return new KeyValueCollection($variablesConfig);
    }
}
