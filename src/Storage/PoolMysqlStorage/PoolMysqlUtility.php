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
use Sunhill\Storage\Exceptions\InvalidTypeException;

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
    
    protected function getArraysOf(string $table): array
    {
        $result = [];
        foreach ($this->structure as $entry) {
            if (($entry->storage_subid == $table) && ($entry->type == 'array')) {
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
    
    protected function addFieldToSchema($schema, \stdClass $field) 
    {
        switch ($field->type) {
            case 'string':
                if (isset($field->max_length)) {
                    $table_field = $schema->string($field->name, $field->max_length);
                } else {
                    $table_field = $schema->string($field->name);
                }
                break;
            case 'array':
                break;
            case 'record':
                $table_field = $schema->text($field->name);
                break;
            case 'boolean':
                $table_field = $schema->bool($field->name);
                break;
            case 'integer':
            case 'text':
            case 'date':
            case 'time':
            case 'datetime':
            case 'float':
                $type = $field->type;
                $table_field = $schema->$type($field->name);
                break;
            default:
                throw new InvalidTypeException("The type '".$field->type."' is unknown.");
        }
        if (isset($field->default)) {
            $table_field->default($field->default);
        }
        if (isset($field->nullable)) {
            $table_field->nullable();
        }        
    }
    
    protected function addArray(string $table, string $index_type, string $element_type)
    {
        Schema::create($table, function($creator) use ($index_type, $element_type)
        {
            $creator->integer('container_id');
            if ($index_type == 'integer') {
                $creator->integer('index');
            } else {
                $creator->string('index');
            }
            $creator->$element_type('element');
        });        
    }
    
    public function getStructureMatrix()
    {
        $result = [];
        foreach ($this->structure as $name => $structure) {
            if ($structure->type == 'array') {
                $name = $this->assembleArrayTableName($structure);
                $result[$name] = $structure;
            } else if (isset($result[$structure->storage_subid])) { 
                $result[$structure->storage_subid][$name] = $structure;
            } else {
                $result[$structure->storage_subid] = [$name => $structure];
            }
        }
        return $result;
    }
    
    public function getDBMatrix()
    {
        
    }
}