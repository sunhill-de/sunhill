<?php
/**
 * @file PoolMysqlStorage.php
 * A persistent pool storage that uses an mysql/mariadb backend as a persistent storage.
 * 
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-12
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: 36.35% (2024-11-13)
 * PSR-State: completed
 */

namespace Sunhill\Storage\MysqlStorage;

use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Query\BasicQuery;
use Illuminate\Support\Facades\DB;
use Sunhill\Storage\ObjectStorage\AttributeStorage;

class MysqlAttributeStorage extends AttributeStorage
{

    protected function isValidID(mixed $id): bool
    {
        return is_int($id);
    }
    
    /**
     * Loads the attribute with id $attribute_id from the table for $attribute_name
     *  
     * {@inheritDoc}
     * @see \Sunhill\Storage\AttributeStorage::doLoadAttribute()
     */
    protected function doLoad($attribute_id)
    {
        $this->id = $attribute_id;
        $table_name = $this->calculateAttributeStorageID($this->attribute_name);
        $entry = DB::table($table_name)->where('id', $attribute_id)->first();
        if (empty($entry)) {
            throw new IDNotFoundException("The id '$attribute_id' was not found");
        }
        $this->setValue($this->attribute_name,$entry->value);
    }
    
    protected function doCommitLoaded()
    {
        
    }
    
    protected function doCommitNew()
    {
        
    }
        
    protected function doWriteAttribute(string $attribute_name,?int $attribute_id)
    {
        
    }
    
    protected function doDelete(mixed $id)
    {
        
    }
 
    protected function doQuery(): BasicQuery
    {
        
    }
    
}
