<?php

declare(strict_types=1);

namespace Jmf\Grid\Grid\Row;

use Jmf\Grid\Grid\KeyValueCollection;
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
            // XML-style list entry: `- { key: ..., value: ... }` (what fixXmlConfig/useAttributeAsKey
            // accepted); normalize it to a `key => value` map entry. A map entry passes through.
            if (is_int($key) && is_array($value)) {
                Assert::keyExists($value, 'key');
                $key = $value['key'];

                Assert::keyExists($value, 'value');
                $value = $value['value'];
            }

            Assert::stringNotEmpty($key);

            $values[$key] = $value;
        }

        return new KeyValueCollection($values);
    }
}
