<?php
/**
 * @file StorableRecordProperty.php
 * Defines a property as a base for all record that can load themself out of a persistent data storage
 * Lang en
 * Reviewstatus: 2024-11-02
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: 
 *
 * Wiki: /PooledRecordProperties
 * tests /tests/Unit/Properties/PooledRecordProperties/*
 */

namespace Sunhill\Properties;

use Sunhill\Storage\AbstractStorage;
use Sunhill\Storage\PersistentSingleStorage;

class StorableRecordProperty extends PersistentRecordProperty
{
    
    /**
     * Loads the record from the storage
     * 
     */
    public function load()
    {
        $this->checkForStorage();
        $storage = $this->getStorage();
        $storage->load();
    }
    
    protected function isValidStorage(AbstractStorage $storage): bool
    {
        return is_a($storage, PersistentSingleStorage::class);
    }
    
}