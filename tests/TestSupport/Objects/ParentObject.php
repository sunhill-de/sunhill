<?php

namespace Sunhill\Tests\TestSupport\Objects;

use Sunhill\Objects\ORMObject;
use Sunhill\Types\TypeInteger;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Types\TypeVarchar;

class ParentObject extends ORMObject
{
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'parent_int');
        $builder->addProperty(TypeVarchar::class,'parent_string')->setMaxLen(3);
        $builder->array('parent_sarray')->getAllowedElementTypes(TypeInteger::class);
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'ParentObject');
        static::addInfo('description', 'A simple object with an int, string and array of int.', true);
        static::addInfo('storage_id', 'parentobjects');
    }
    
}