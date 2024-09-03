<?php

namespace Sunhill\Properties\Tests\Unit\Objects\PersistantRecord\Samples;

use Sunhill\Properties\Objects\AbstractPersistantRecord;
use Sunhill\Properties\Objects\ObjectDescriptor;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Facades\Properties;
use Sunhill\Properties\Types\TypeVarchar;

class EmptyGrandChildRecord extends EmptyChildRecord
{

    public static $called_emptygrandchild = 0;
    
    protected static function initializeProperties(ObjectDescriptor $descriptor)
    {
        static::$called_emptygrandchild++;
    }
    
}