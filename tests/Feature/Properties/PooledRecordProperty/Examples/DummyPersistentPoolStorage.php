<?php

namespace Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples;

use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Storage\PersistentPoolStorage;

class DummyPersistentPoolStorage extends PersistentPoolStorage
{
    
    static public $persistent_data = [];
    
    public function __construct()
    {
        parent::__construct();
        static::$persistent_data  = 
            [
                'poolA'=>[
                    ['parent_str'=>'AAA','parent_int'=>111],
                    ['parent_str'=>'BBB','parent_int'=>222],
                ],
                'poolB'=>[
                    ['parent_str'=>'ABC','parent_int'=>123,'child_str'=>'ABA'],
                    ['parent_str'=>'BCE','parent_int'=>234,'child_str'=>'BCB'],
                ]                
            ];
    }
    
    protected function doLoad(mixed $id)
    {
        if (($id < 0) || ($id > 1)) {
            throw new IDNotFoundException("The id was not found.");
        }
        $this->structureNeeded();
        $this->values = [];
        foreach($this->structure as $property => $descriptor) {
            $this->values[$property] = static::$persistent_data[$descriptor->storage_id][$id][$property];
        }
    }
    
    protected function doDelete(mixed $id)
    {
        if (($id < 0) || ($id > 1)) {
            throw new IDNotFoundException("The id was not found.");
        }
        $this->structureNeeded();
        $storage_ids = [];
        foreach ($this->structure as $property => $descriptor) {
            if (!in_array($descriptor->storage_id,$storage_ids)) {
                $storage_ids[] = $descriptor->storage_id;
            }
        }
        foreach ($storage_ids as $storage_id) {
            unset(static::$persistent_data[$storage_id][$id]);
        }
    }
    
    protected function isValidID(mixed $id): bool
    {
        return is_int($id);    
    }
    
    protected function doCommitLoaded()
    {
        $modified = $this->getModifiedValues();
        foreach ($modified as $key => $value) {
            static::$persistent_data[$this->structure[$key]->storage_id][$this->getID()][$key] = $value->new;
        }            
    }
    
    protected function doCommitNew()
    {
        $id = count(static::$persistent_data['poolA']);
        foreach ($this->values as $key => $value) {
            if (isset(static::$persistent_data[$this->structure[$key]->storage_id][$id])) {
                static::$persistent_data[$this->structure[$key]->storage_id][$id][$key] = $value;
            } else {
                static::$persistent_data[$this->structure[$key]->storage_id][$id] = [$key => $value];
            }
        }
        return $id;
    }
        
}