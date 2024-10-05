<?php

namespace Sunhill\Tests\TestSupport\Storages;

use Sunhill\Storage\GroupCacheStorage;

class DummyGroupCacheStorage extends GroupCacheStorage
{
    public static $call_count = 0;
    
    protected function readValues(): array
    {
        self::$call_count++;
        return ['keyA'=>'ValueA','keyB'=>'ValueB'];
    }
}
