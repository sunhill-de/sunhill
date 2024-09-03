<?php

namespace Sunhill\Tests\TestSupport\Storages;

use Sunhill\Storage\SimpleStorage;

class TestStorage1 extends SimpleStorage
{
    protected function readValues(): array
    {
        return ['element1'=>'ValueA','element2'=>'valueB'];
    }
}

