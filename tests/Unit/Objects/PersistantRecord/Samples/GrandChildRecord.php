<?php

namespace Sunhill\Properties\Tests\Unit\Objects\PersistantRecord\Samples;

use Sunhill\Properties\Objects\AbstractPersistantRecord;
use Sunhill\Properties\Objects\ObjectDescriptor;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Facades\Properties;
use Sunhill\Properties\Types\TypeVarchar;

class GrandChildRecord extends ChildRecord
{

    public static $called_grandchild = 0;
    
    protected static function initializeProperties(ObjectDescriptor $descriptor)
    {
        static::$called_grandchild++;
    }
    
}