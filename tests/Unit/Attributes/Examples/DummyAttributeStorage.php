<?php

namespace Sunhill\Tests\Unit\Attributes\Examples;

use Sunhill\Attributes\AbstractAttributeStorage;

class DummyAttributeStorage extends AbstractAttributeStorage
{
    public static $attributes = [
        'test_attribute' => ['id' => 1, 'name' => 'test_attribute', 'type' => 'integer', 'values' => [
            1 => ['container_id' => 1, 'value' => 111],
            2 => ['container_id' => 2, 'value' => 222],
        ],
        ],
        'str_attribute' => ['id' => 2, 'name' => 'str_attribute', 'type' => 'string', 'values' => [
            1 => ['container_id' => 1, 'value' => 'abc'],
            2 => ['container_id' => 2, 'value' => 'def'],
        ],
        ],
    ];

    protected function getAttributeValue(string $attribute_storage, int $object_id)
    {
        $attribute = substr($attribute_storage, 5);
        if (! isset(static::$attributes[$attribute]['values'][$object_id])) {
            return null;
        }

        return static::$attributes[$attribute]['values'][$object_id]['value'];
    }

    public function searchAttribute(array $criteria): ?\stdClass
    {
        $search_key = array_keys($criteria)[0];
        $search_value = array_values($criteria)[0];
        foreach (static::$attributes as $key => $value) {
            if ($value[$search_key] == $search_value) {
                $result = new \stdClass;
                $result->id = $value['id'];
                $result->name = $value['name'];
                $result->type = $value['type'];

                return $result;
            }
        }

        return null;
    }

    protected function storeAttributeValue(string $attribute_storage, int $object_id, mixed $value)
    {
        $attribute = substr($attribute_storage, 5);
        static::$attributes[$attribute]['values'][$object_id]['value'] = $value;
    }

    protected function unsetAttributeValue(string $attribute_storage, int $object_id)
    {
        $attribute = substr($attribute_storage, 5);
        unset(static::$attributes[$attribute]['values'][$object_id]);
    }
}
