<?php

namespace Sunhill\Tests\TestSupport\Storages;

use Sunhill\Storage\PersistentStorage;
use Sunhill\Storage\Exceptions\IDNotFoundException;

class DummyPersistentStorage extends PersistentStorage
{
    
    static public $persistent_data = [];
    
    public function __construct()
    {
        parent::__construct();
        static::$persistent_data  = [
            ['str_field'=>'ABC','int_field'=>11,'float_field'=>1.11,'array_field'=>[1,2,3]],  
            ['str_field'=>'DEF','int_field'=>22,'float_field'=>2.22,'array_field'=>[4,5,6]],
            ['str_field'=>'GHI','int_field'=>33,'float_field'=>3.33,'array_field'=>[7,8,9]],
            ['str_field'=>'JKL','int_field'=>44,'float_field'=>4.44,'array_field'=>[10,11,12]],
        ];    
    }
    
    protected function doLoad(mixed $id)
    {
        if (($id < 0) || ($id > 3)) {
            throw new IDNotFoundException("The id was not found.");
        }
        $this->values = static::$persistent_data[$id];
    }
    
    protected function isValidID(mixed $id): bool
    {
        return is_int($id);    
    }
    
    protected function doCommit()
    {
        
    }
    
    protected function doMigrate()
    {
        // Does nothing by default
    }
    
}