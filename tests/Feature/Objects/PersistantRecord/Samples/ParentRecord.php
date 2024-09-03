<?php

namespace Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples;

use Sunhill\Properties\Objects\AbstractPersistantRecord;
use Sunhill\Properties\Objects\ObjectDescriptor;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Facades\Properties;

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