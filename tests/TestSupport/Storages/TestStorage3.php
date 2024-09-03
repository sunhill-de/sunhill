<?php

namespace Sunhill\Properties\Tests\TestSupport\Storages;

use Sunhill\Properties\Storage\SimpleWriteableStorage;

class TestStorage3 extends SimpleWriteableStorage
{
    protected function readValues(): array
    {
        return ['stringkey'=>'ValueA','floatkey'=>3.2,'intkey'=>3,'boolkey'=>1,'textkey'=>'Lorep ipsum','datekey'=>'2023-12-24','timekey'=>'11:12:13','datetimekey'=>'2023-12-24 11:12:13'];
    }
}
