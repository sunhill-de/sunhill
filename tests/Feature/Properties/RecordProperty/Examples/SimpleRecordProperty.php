<?php

namespace Sunhill\Tests\Feature\Properties\RecordProperty\Examples;

use Sunhill\Properties\RecordProperty;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Tests\TestSupport\Storages\DummyStorage;

class SimpleRecordProperty extends RecordProperty
{
    
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->integer('test_int');
        $builder->string('test_str');        
    }
    
    protected function createStorage(): ?AbstractStorage
    {
        return new DummyStorage();
    }
    
}