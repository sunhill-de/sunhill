<?php
/**
 * @file PersistentRecordProperty.php
 * A base for PooledRecordProperty and StorableRecordProperty. It defines a create method, that
 * prefills all values with their default value.
 * 
 * Lang en
 * Reviewstatus: 2024-11-02
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: 100 % (2024-11-13)
 *
 * Wiki: /PooledRecordProperties
 * tests /tests/Unit/Properties/PooledRecordProperties/*
 */

namespace Sunhill\Properties;

class PersistentRecordProperty extends RecordProperty
{
    
    /**
     * Creates a empty record, prefills the storage with the default values
     * 
     */
    public function create()
    {
        foreach ($this as $key => $property) {
            if ($property->hasDefault()) {
                $this->$key = $property->getDefault();
            }
        }
    }
    
}