<?php

namespace Sunhill\Tests\Feature\Objects\PersistantRecord\Samples;

use Sunhill\Objects\AbstractPersistantRecord;
use Sunhill\Objects\ObjectDescriptor;
use Sunhill\Types\TypeInteger;
use Sunhill\Facades\Properties;
use Sunhill\Types\TypeVarchar;

class EmptyGrandChildRecord extends EmptyChildRecord
{

    protected static function initializeProperties(ObjectDescriptor $descriptor)
    {
        $descriptor->integer('grandchildint');
        $descriptor->varchar('grandchildvarchar');
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'EmptyGrandChildRecord');
        static::addInfo('storage_id', 'emptygrandchildrecords');
    }
    
}