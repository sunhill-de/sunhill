<?php

namespace Sunhill\Tests\Unit\Objects\PersistantStorage\Samples;

use Sunhill\Properties\AbstractProperty;
use Sunhill\Objects\AbstractStorageAtom;

class DummyStorageAtom extends AbstractStorageAtom
{
    
    protected static $prefix = 'store';
    
    protected function handleRecord(string $storage_id, array $descriptor, $additional1 = null, $additional2 = null)
    {
        
    }
    
    protected function handleDirectory(string $storage_id, $additional = null)
    {
        
    }
    
    protected function handleTags($additional = null)
    {
        
    }
    
    protected function handleAttributes($additional = null)
    {
        
    }
    
}