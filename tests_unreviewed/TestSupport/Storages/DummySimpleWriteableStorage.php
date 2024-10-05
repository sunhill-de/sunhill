<?php

namespace Sunhill\Tests\TestSupport\Storages;

use Sunhill\Storage\SimpleWriteableStorage;

class DummySimpleWriteableStorage extends SimpleWriteableStorage
{
    public $values = ['keyA'=>'ValueA','keyB'=>'ValueB','keyC'=>['ABC','DEF']];
    
    protected function readValues(): array
    {
        return $this->values;
    }

}
