<?php

namespace Sunhill\Properties\Tests\Unit\Objects\RecordSetupWorker\Samples;

namespace Sunhill\Properties\Tests\Unit\Managers\Samples;

use Sunhill\Properties\Objects\AbstractPersistantRecord;

class First extends AbstractPersistantRecord
{
    
    public static function getInfo($key, $default = null)
    {
        switch ($key) {
            case 'storage_id':
                return 'teststorage';
            case 'name':
                return 'First';
            default:
                throw new \Exception("Unexpected: $key");                
        }
    }
    
    public static function handleInheritance(): string
    {
        return 'include';
    }
}