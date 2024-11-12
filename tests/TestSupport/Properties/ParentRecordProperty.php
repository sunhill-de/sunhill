<?php

namespace Sunhill\Tests\TestSupport\Properties;

use Sunhill\Properties\RecordProperty;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Types\TypeInteger;
use Sunhill\Types\TypeVarchar;

class ParentRecordProperty extends RecordProperty
{
    
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'parent_int');
        $builder->addProperty(TypeVarchar::class,'parent_string');
    }
  
    public static function setInclusion(string $inclusion)
    {
        static::$inherited_inclusion = $inclusion;
    }
    
    public static function getStorageID(): string
    {
        return 'parent';
    }
}