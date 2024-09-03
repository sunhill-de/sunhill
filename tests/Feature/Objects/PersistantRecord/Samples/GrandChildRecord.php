<?php

namespace Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples;

use Sunhill\Properties\Objects\AbstractPersistantRecord;
use Sunhill\Properties\Objects\ObjectDescriptor;
use Sunhill\Properties\Types\TypeInteger;
use Sunhill\Properties\Facades\Properties;
use Sunhill\Properties\Types\TypeVarchar;

class GrandChildRecord extends ChildRecord
{

    protected static function initializeProperties(ObjectDescriptor $descriptor)
    {
        $descriptor->integer('grandchildint');
        $descriptor->varchar('grandchildvarchar');
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'GrandChildRecord');
        static::addInfo('storage_id', 'grandchildrecords');
    }
    
}