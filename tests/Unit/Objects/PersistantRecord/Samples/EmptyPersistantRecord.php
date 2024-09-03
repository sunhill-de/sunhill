<?php

namespace Sunhill\Tests\Unit\Objects\PersistantRecord\Samples;

use Sunhill\Objects\AbstractPersistantRecord;
use Sunhill\Objects\ObjectDescriptor;

class EmptyPersistantRecord extends AbstractPersistantRecord
{
    
    protected static function initiaölizeProperties(ObjectDescriptor $descriptor)
    {
        
    }
    
    public static $handle_inheritance = 'include';
    
    protected static function handleInheritance(): string
    {
        return self::$handle_inheritance;    
    }

    protected static function setupInfos()
    {
        static::addInfo('name', 'EmptyPersistantRecord');
        static::addInfo('description', 'A base test class for an persitant record.', true);
        static::addInfo('storage_id', 'teststorage');
    }
        
}