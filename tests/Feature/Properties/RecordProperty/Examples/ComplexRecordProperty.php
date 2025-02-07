<?php

namespace Sunhill\Tests\Feature\Properties\RecordProperty\Examples;

use Sunhill\Properties\RecordProperty;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Tests\TestSupport\Storages\DummyStorage;
use Sunhill\Types\TypeInteger;

class ComplexRecordProperty extends RecordProperty
{
    
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->integer('complex_int');
        $builder->string('complex_str');
        $builder->includeRecord(SimpleRecordProperty::class);
        $builder->referRecord(SimpleRecordProperty::class, 'reference_record');
        $builder->array('complex_array_of_int')->setAllowedElementType(TypeInteger::class);
    }
    
    protected function createStorage(): ?AbstractStorage
    {
        return new DummyStorage();
    }
    
}