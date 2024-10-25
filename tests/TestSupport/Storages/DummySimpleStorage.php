<?php

namespace Sunhill\Tests\TestSupport\Storages;

use Sunhill\Storage\SimpleStorage;

class DummySimpleStorage extends SimpleStorage
{
    protected function readValues(): array
    {
        return ['keyA'=>'ValueA','keyB'=>'ValueB','keyC'=>['ABC','DEF'],'keyD'=>3.56,'keyE'=>[1,2,3]];
    }
}
