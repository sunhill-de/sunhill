<?php

namespace Sunhill\Properties\Tests\Unit\Objects\PersistantRecord\Samples;

use Sunhill\Properties\Objects\AbstractPersistantRecord;
use Sunhill\Properties\Objects\ObjectDescriptor;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Facades\Properties;

class ParentRecord extends AbstractPersistantRecord
{

    public static $handle_inheritance = 'include';
    
    public static $called_parent = 0;
    
    protected static function handleInheritance(): string
    {
        return self::$handle_inheritance;    
    }

    protected static function initializeProperties(ObjectDescriptor $descriptor)
    {
        static::$called_parent++;
    }
}