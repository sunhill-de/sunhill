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

namespace Sunhill\Storage\MysqlStorage\ObjectUtils;

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
        foreach ($this->structure->elements as $entry) {
            if (!in_array($entry->storage_subid,$result)) {
                $result[] = $entry->storage_subid;
            }
        }
        return $result;
    }
    
    protected function getFieldsOf(string $name)
    {
        $result = [];
        foreach ($this->structure->elements as $entry) {
            if ($entry->storage_subid == $name) {
                $result[] = $entry;
            }
        }
        return $result;
    }
    
    protected function getArrays(): array
    {
        $result = [];
        foreach ($this->structure->elements as $entry) {
            if ($entry->type == 'array') {
                $result[] = $entry;
            }
        }
        return $result;
    }
    
    protected function getArraysOf(string $table): array
    {
        $result = [];
        foreach ($this->structure->elements as $entry) {
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
        if (!isset($result['_classname'])) {
            $result['_classname'] = $this->structure->options['name']->value;
        }
        return $result;
    }
    
    protected function getTableFields(string $table, array $values)
    {
        $fields = [];
        foreach ($this->structure->elements as $name => $structure) {
            if (($structure->storage_subid == $table) && ($structure->type !== 'array')) {
               if (array_key_exists($name, $values)) {
                   $fields[$name] = $this->getField($values[$name]);
               }
            }
        }
        return $fields;
    }
    
    protected function createField($schema, string $name, string $type, $additional = null)
    {
        switch (strtolower($type)) {
            case 'string':
                if (!is_null($additional)) {
                    $table_field = $schema->string($name, $additional);
                } else {
                    $table_field = $schema->string($name);
                }
                break;
            case 'array':
                break;
            case 'record':
                $table_field = $schema->text($name);
                break;
            case 'boolean':
                $table_field = $schema->bool($name);
                break;
            case 'integer':
            case 'text':
            case 'date':
            case 'time':
            case 'datetime':
            case 'float':
                $table_field = $schema->$type($name);
                break;
            default:
                throw new InvalidTypeException("The type '$type' is unknown.");
        }
        return $table_field;
    }
    
    protected function addFieldToSchema($schema, \stdClass $field) 
    {
        $table_field = $this->createField($schema, $field->name, $field->type, isset($field->max_length)?$field->max_length:null);
        if (isset($field->default)) {
            $table_field->default($field->default);
        }
        if (isset($field->nullable)) {
            $table_field->nullable();
        }        
        return $table_field;
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
        foreach ($this->structure->elements as $name => $structure) {
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