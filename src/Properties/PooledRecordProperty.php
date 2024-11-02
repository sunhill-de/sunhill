<?php
/**
 * @file PooledRecordProperty.php
 * Defines a property as a base for all record that can load themself out of a pool of data
 * Lang en
 * Reviewstatus: 2024-10-24
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: 
 *
 * Wiki: /PooledRecordProperties
 * tests /tests/Unit/Properties/PooledRecordProperties/*
 */

namespace Sunhill\Properties;

use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Properties\Exceptions\WrongStorageSetException;

class PooledRecordProperty extends RecordProperty
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
        if (!is_a($storage, PersistentPoolStorage::class)) {
            throw new WrongStorageSetException("PersistentPoolStorage expected but not set.");
        }
        $storage->load($id);
    }
    
    /**
     * Creates a empty record, prefills the storage with the default values
     * 
     */
    public function create()
    {
        
    }
    
    public function getID(): ?int
    {
        $this->checkForStorage();
        $storage = $this->getStorage();
        if (!is_a($storage, PersistentPoolStorage::class)) {
            throw new WrongStorageSetException("PersistentPoolStorage expected but not set.");
        }
        return $storage->getID();
    }
}