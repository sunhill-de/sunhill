<?php

namespace Sunhill\Tests\Unit\Objects\PersistantRecord\Samples;

use Sunhill\Objects\AbstractPersistantRecord;
use Sunhill\Objects\ObjectDescriptor;
use Sunhill\Types\TypeInteger;
use Sunhill\Facades\Properties;
use Sunhill\Types\TypeVarchar;

class ChildRecord extends ParentRecord
{

    public static $called_child = 0;
    
    protected static function initializeProperties(ObjectDescriptor $descriptor)
    {
        static::$called_child++;
    }
    
}