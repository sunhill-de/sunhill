<?php

namespace Sunhill\Properties\Tests\Feature\Objects\PersistantRecord\Samples;

class EmptyChildRecord extends ParentRecord
{

    protected static function setupInfos()
    {
        static::addInfo('name', 'EmptyChildRecord');
        static::addInfo('storage_id', 'emptychildrecords');
    }
    
}