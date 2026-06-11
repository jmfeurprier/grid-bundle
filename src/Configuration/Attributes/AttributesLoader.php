<?php

declare(strict_types=1);

namespace Jmf\Grid\Configuration\Attributes;

use Jmf\Grid\Configuration\KeyValueCollection;
use Webmozart\Assert\Assert;

readonly class AttributesLoader
{
    /**
     * @param array<string, mixed> $config
     */
    public function load(array $config): KeyValueCollection
    {
        Assert::isMap($config);

        if (!isset($config['attributes'])) {
            return KeyValueCollection::createEmpty();
        }

        $attributesConfig = $config['attributes'];

        Assert::isArray($attributesConfig);

        $values = [];

        foreach ($attributesConfig as $key => $value) {
            if (is_int($key) && is_array($value)) {
                Assert::keyExists($value, 'key');
                $key = Assert::stringNotEmpty($value['key']);

                Assert::keyExists($value, 'value');
                $value = $value['value'];

                $values[$key] = $value;
            }
        }


        return new KeyValueCollection($attributesConfig);
    }
}
