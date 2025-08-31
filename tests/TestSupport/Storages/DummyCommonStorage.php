<?php

namespace Sunhill\Tests\TestSupport\Storages;

use Sunhill\Storage\CommonStorage;

class DummyCommonStorage extends CommonStorage
{
    public function __construct()
    {
        parent::__construct();
        $this->values = ['str_value' => 'ABC', 'array_value' => [11, 22, 33]];
    }

    protected function doSetValue(string $name, $value) {}

    protected function doSetIndexedValue(string $name, $index, $value) {}

    protected function doUnsetIndexedValue(string $name, $index)
    {
        // Should not be called
    }
}
