<?php
/**
 * @file PoolMysqlItility.php
 * A base for the helping classes
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-12
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: 100 % (2024-11-13)
 * PSR-State: completed
 */

namespace Sunhill\Storage\PoolMysqlStorage;

use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\StorageTableMissingException;

class PoolMysqlUtility
{
    protected $structure;
    
    public function __construct($structure)
    {
        $this->structure = $structure;
    }
    
    /**
     * Returns all distinct storage sub ids.
     * @return array
     */
    protected function getStorageSubids(): array
    {
        $result = [];
        foreach ($this->structure as $entry) {
            if (!in_array($entry->storage_subid,$result)) {
                $result[] = $entry->storage_subid;
            }
        }
        return $result;
    }
    
    protected function getArrays(): array
    {
        $result = [];
        foreach ($this->structure as $entry) {
            if ($entry->type == 'array') {
                $result[] = $entry;
            }
        }
        return $result;
    }
    
    protected function assembleArrayTableName(\stdClass $info): string
    {
        return $info->storage_subid.'_'.$info->name;
    }
    
    protected function tableNeeded(string $name)
    {
        if (!Schema::hasTable($name)) {
            throw new StorageTableMissingException("The table '$name' is expected but missing.");
        }
    }
    
    protected function getObjectFields(array $values,array $modified = [])
    {
        $object_fields = ['_classname','_uuid','_read_cap','_modify_cap','_delete_cap','_created_at','_modified_at'];
        $result = [];    

        foreach ($object_fields as $field) {
            if (empty($modified) || in_array($field, $modified)) {
                $result[$field] = $values[$field];
            }
        }
        return $result;
    }
}