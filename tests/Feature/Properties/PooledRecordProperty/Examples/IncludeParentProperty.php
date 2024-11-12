<?php

namespace Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples;

use Sunhill\Properties\PooledRecordProperty;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Storage\AbstractStorage;

class IncludeParentProperty extends PooledRecordProperty
{
  
    protected static $inherited_inclusion = 'include';
    
    public static function getStorageID(): string
    {
        return 'poolA'; // Per default nothing
    }
    
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->integer('parent_int');
        $builder->string('parent_str');
    }
    
    protected function createStorage(): ?AbstractStorage
    {
        $storage = new DummyPersistentPoolStorage();
        $storage->setStructure($this->getStructure()->elements);
        return $storage;
    }
    
    
}