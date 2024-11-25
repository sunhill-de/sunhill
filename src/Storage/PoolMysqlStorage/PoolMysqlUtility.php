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
use function PHPUnit\Framework\arrayHasKey;

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
    
    protected function getFieldsOf(string $name)
    {
        $result = [];
        foreach ($this->structure as $entry) {
            if ($entry->storage_subid == $name) {
                $result[] = $entry;
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
    
    private function getField($field)
    {
        if (is_a($field, \stdClass::class)) {
            return $field->new;
        }
        return $field;
    }
    
    protected function getObjectFields(array $values)
    {
        $object_fields = ['_classname','_uuid','_read_cap','_modify_cap','_delete_cap','_created_at','_updated_at'];
        $result = [];    

        foreach ($object_fields as $field) {
            if (array_key_exists($field, $values)) {
                $result[$field] = $this->getField($values[$field]);
            }
        }
        return $result;
    }
    
    protected function getTableFields(string $table, array $values)
    {
        $fields = [];
        foreach ($this->structure as $name => $structure) {
            if (($structure->storage_subid == $table) && ($structure->type !== 'array')) {
               if (array_key_exists($name, $values)) {
                   $fields[$name] = $this->getField($values[$name]);
               }
            }
        }
        return $fields;
    }
}