<?php

namespace Sunhill\Tests\Feature\Objects\PersistantRecord\Samples;

use Sunhill\Objects\AbstractPersistantRecord;
use Sunhill\Objects\ObjectDescriptor;
use Sunhill\Types\TypeInteger;
use Sunhill\Facades\Properties;

class ParentRecord extends AbstractPersistantRecord
{

    public static $handle_inheritance = 'include';
        
    protected static function handleInheritance(): string
    {
        return self::$handle_inheritance;    
    }

    protected static function initializeProperties(ObjectDescriptor $descriptor)
    {
        $descriptor->integer('parentint');
        $descriptor->varchar('parentvarchar');
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'ParentRecord');
        static::addInfo('storage_id', 'parentrecords');
    }
}