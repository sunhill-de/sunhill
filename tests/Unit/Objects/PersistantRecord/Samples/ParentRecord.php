<?php

namespace Sunhill\Tests\Unit\Objects\PersistantRecord\Samples;

use Sunhill\Objects\AbstractPersistantRecord;
use Sunhill\Objects\ObjectDescriptor;
use Sunhill\Types\TypeInteger;
use Sunhill\Facades\Properties;

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