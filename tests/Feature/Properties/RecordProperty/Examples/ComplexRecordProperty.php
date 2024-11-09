<?php

namespace Sunhill\Tests\Feature\Properties\RecordProperty\Examples;

use Sunhill\Properties\RecordProperty;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Tests\TestSupport\Storages\DummyStorage;

class ComplexRecordProperty extends RecordProperty
{
    
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->integer('complex_int');
        $builder->string('complex_str');
        $builder->includeRecord(SimpleRecordProperty::class);
        $builder->referRecord(SimpleRecordProperty::class, 'reference_record');
    }
    
    protected function createStorage(): ?AbstractStorage
    {
        return new DummyStorage();
    }
    
}