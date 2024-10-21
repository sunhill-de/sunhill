<?php

namespace Sunhill\Tests\TestSupport\Storages;

use Sunhill\Storage\PersistentSingleStorage;

class DummyPersistentSingleStorage extends PersistentSingleStorage
{
    
    static public $persistent_data = [];
    
    public function __construct()
    {
        parent::__construct();
        static::$persistent_data  = ['str_field'=>'ABC','int_field'=>11,'float_field'=>1.11,'array_field'=>[1,2,3]];  
    }
    
    protected function doLoad()
    {
        $this->values = static::$persistent_data;
    }
    
    protected function doCommit()
    {
        $modified = $this->getModifiedValues();
        foreach ($modified as $key => $value) {
            static::$persistent_data[$key] = $value->new;
        }            
    }
    
    protected function doMigrateNew()
    {
        $this->structureNeeded();
        static::$persistent_data = 'migrated new';
    }
    
    protected function doMigrateUpdate()
    {
        $this->structureNeeded();
        static::$persistent_data = 'migration changed';
    }
    
    protected function isAlreadyMigrated(): bool
    {
        return is_array(static::$persistent_data);
    }
    
    protected function isMigrationUptodate(): bool
    {
        $this->structureNeeded();
        return isset(static::$persistent_data['str_field']);
    }
    
}