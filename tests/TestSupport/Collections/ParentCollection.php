<?php

namespace Sunhill\Tests\TestSupport\Collections;

use Sunhill\Types\TypeInteger;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Types\TypeVarchar;
use Sunhill\Objects\Collection;

class ParentCollection extends Collection
{
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'parent_int');
        $builder->addProperty(TypeVarchar::class,'parent_string')->setMaxLen(3);
        $builder->array('parent_sarray')->setAllowedElementType(TypeInteger::class);
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'ParentCollection');
        static::addInfo('description', 'A simple collection with an int, string and array of int.', true);
        static::addInfo('storage_id', 'parentcollections');
    }
    
}