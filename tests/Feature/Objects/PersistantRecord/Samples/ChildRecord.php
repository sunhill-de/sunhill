<?php

namespace Sunhill\Tests\Feature\Objects\PersistantRecord\Samples;

use Sunhill\Objects\ObjectDescriptor;

class ChildRecord extends ParentRecord
{

    protected static function initializeProperties(ObjectDescriptor $descriptor)
    {
        $descriptor->integer('childint');
        $descriptor->varchar('childvarchar');
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'ChildRecord');
        static::addInfo('storage_id', 'childrecords');
    }
    
}