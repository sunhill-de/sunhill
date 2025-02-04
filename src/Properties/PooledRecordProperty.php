<?php
/**
 * @file PooledRecordProperty.php
 * Defines a property as a base for all record that can load themself out of a pool of data
 * Lang en
 * Reviewstatus: 2024-10-24
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: 100 % (2024-11-13)
 *
 * Wiki: /PooledRecordProperties
 * tests /tests/Unit/Properties/PooledRecordProperties/*
 */

namespace Sunhill\Properties;

use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Properties\Exceptions\WrongStorageSetException;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Query\BasicQuery;

class PooledRecordProperty extends PersistentRecordProperty
{
    
    /**
     * Loads the record with the given id out of the pool
     * 
     * @param unknown $id
     */
    public function load($id)
    {
        $this->checkForStorage();
        $storage = $this->getStorage();
        $storage->setStructure($this->getStructure());
        $storage->load($id);
    }
    
    public function IDexists($id): bool
    {
        $this->checkForStorage();
        $storage = $this->getStorage();
        $storage->setStructure($this->getStructure());
        return $storage->IDExists($id);        
    }
    
    protected function isValidStorage(AbstractStorage $storage): bool
    {
        return is_a($storage, PersistentPoolStorage::class);
    }
    
    public function getID(): mixed
    {
        $this->checkForStorage();
        $storage = $this->getStorage();
        return $storage->getID();
    }
    
    public function delete($id)
    {
        $this->checkForStorage();
        $storage = $this->getStorage();
        $storage->delete($id);        
    }
    
    /**
     * Executes a migration for this kind of pooled record
     */
    public static function migrate()
    {
        $dummy = new static();
        $storage = $dummy->getStorage();
        $storage->migrate();
    }
    
    /**
     * Executes a query on data of this kind of object
     *
     * @return BasicQuery
     */
    public static function query(): BasicQuery
    {
        $dummy = new static();  // An instance of an object is necessary because the storage system works only on instances
        $storage = $dummy->getStorage();
        return $storage->query();
    }
        
}