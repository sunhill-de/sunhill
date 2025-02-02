<?php

namespace Sunhill\Tests\TestSupport\Objects;

use Sunhill\Types\TypeInteger;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Types\TypeVarchar;

class ChildCollection extends ParentCollection
{
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'child_int');
        $builder->addProperty(TypeVarchar::class,'child_string')->setMaxLen(3);
        $builder->array('child_sarray')->setAllowedElementTypes(TypeInteger::class);
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'ChildCollection');
        static::addInfo('description', 'A simple derrived collection with an int, string and array of int.', true);
        static::addInfo('storage_id', 'childcollections');
    }
    
}