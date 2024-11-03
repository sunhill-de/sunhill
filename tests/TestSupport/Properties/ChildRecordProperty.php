<?php

namespace Sunhill\Tests\TestSupport\Properties;

use Sunhill\Properties\ElementBuilder;
use Sunhill\Types\TypeInteger;
use Sunhill\Types\TypeVarchar;

class ChildRecordProperty extends ParentRecordProperty
{
    
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'child_int');
        $builder->addProperty(TypeVarchar::class,'child_string');
    }
        
}