<?php

namespace Sunhill\Tests\Unit\Objects\PersistantRecord\Samples;

use Sunhill\Objects\AbstractPersistantRecord;
use Sunhill\Objects\ObjectDescriptor;
use Sunhill\Types\TypeInteger;
use Sunhill\Facades\Properties;
use Sunhill\Types\TypeVarchar;

class GrandChildRecord extends ChildRecord
{

    public static $called_grandchild = 0;
    
    protected static function initializeProperties(ObjectDescriptor $descriptor)
    {
        static::$called_grandchild++;
    }
    
}