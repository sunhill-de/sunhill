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
 * Coverage Unit: 87.90  (2025-06-06)
 * PSR-State: completed
 */

namespace Sunhill\Storage\MysqlStorage;

use Sunhill\Query\QueryParser\QueryNode;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Sunhill\Storage\Exceptions\InvalidTypeException;
use Sunhill\Storage\AbstractObjectStorage\AbstractObjectStorage;
use Sunhill\Parser\Executor;

class MysqlObjectStorage extends AbstractObjectStorage
{

    /**
     * Tests if the given id is stored in objects. Every object has to to stored there so this is the best place to look for it
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::IDExists()
     */
    public function IDExists($id): bool
    {
        $result = DB::table('objects')->where('id',$id)->first();
        return !empty($result);
    }
    
    /**
     * Updates the storage with the subid. It uses $key to identiy the record(s) and sets the givenvalues
     *
     * @param unknown $key
     * @param unknown $values
     */
    protected function updateStorageSubid(string $subid, int $key, array $values, string $key_field = 'id')
    {
        $this->tableNeeded($subid);
        DB::table($subid)->where($key_field,'=',$key)->update($values);
    }
    
    /**
     * Deletes all references to key from the given
     *
     * @param unknown $key
     */
    protected function deleteStorageSubid(string $subid, int $key, string $key_field = 'id')
    {
        $this->tableNeeded($subid);
        DB::table($subid)->where($key_field,$key)->delete();
    }
    
    protected function insertObjects(array $values): int
    {
        $this->tableNeeded('objects');
        $id = DB::table('objects')->insertGetId($this->getObjectFields($values));
        $this->setID($id);
        return $id;
    }
    
    /**
     * Inserts into the given storage subid the given values. If value is a array of arrays then insert every entry as a separate record
     *
     * @param string $subid
     * @param array $values
     */
    protected function insertStorageSubid(string $subid, array $values)
    {
        $this->tableNeeded($subid);
        DB::table($subid)->insert($values);
    }
    
    /**
     * Loads from the given storage subif the values with the given key
     *
     * @param string $subid
     * @param int $key
     * @return array
     */
    protected function loadStorageSubid(string $subid, int $key, string $key_field = 'id'): array|\Traversable|\stdClass
    {
        $this->tableNeeded($subid);
        return DB::table($subid)->where($key_field, $key)->get();
    }

    protected function doExecuteQuery(QueryNode $node)
    {
        $executor = new MysqlQueryExecutor();
        return $executor->executeQuery($node);
    }

    private function getStringMaxLen(string $storage_subid, string $column_name): string|int
    {
        return '*'; // @todo This is only true for sqlite
    }
    
    private function getDescriptorForColumn(string $storage_subid, string $column_name): \stdClass
    {
        $result = new \stdClass();
        switch ($result->type = DBTableColumnType($storage_subid, $column_name)) {
            case 'string':
                $result->max_len = $this->getStringMaxLen($storage_subid, $column_name);
                break;
        }
        return $result;
    }
    
    private function tableIsArray(array|string $columns_or_tablename): bool
    {
        if (is_array($columns_or_tablename)) {
            return !in_array('id', $columns_or_tablename);
        }
        return !in_array('id',Schema::getColumnListing($columns_or_tablename));
    }
    
    private function getCurrentArrayStructure(string $storage_subid): \stdClass
    {
        $result = new \stdClass();
        $result->index_type =  $this->getDescriptorForColumn($storage_subid, 'index');
        $result->element_type = $this->getDescriptorForColumn($storage_subid, 'element');
        $result->type = 'array';        
        return $result;
    }
    
    private function getCurrentObjectStructure(string $storage_subid, array $columns): \stdClass
    {
        $result = new \stdClass();
        
        foreach ($columns as $column) {
            $result->$column = $this->getDescriptorForColumn($storage_subid, $column);
        }
        
        return $result;
    }
    
    protected function getCurrentStorageStructure(string $storage_subid): \stdClass|string
    {
        $columns = Schema::getColumnListing($storage_subid);
        if ($this->tableIsArray($columns)) {
            return $this->getCurrentArrayStructure($storage_subid);
        } else {
            return $this->getCurrentObjectStructure($storage_subid, $columns);
        }
    }
    
    /**
     * Returns the tables that belong to the given storage subid.
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\AbstractObjectStorage::getStoragesFor()
     */
    protected function getStoragesFor(string $storage_subid): array
    {
        $result = [];
        foreach (DB::connection()->getSchemaBuilder()->getTables() as $db_table) {
            if (Str::startsWith($db_table['name'], $storage_subid)) {
                $result[] = $db_table['name'];
            }
        }        
        return $result;
    }
    
    protected function dropStorage(string $storage_name)
    {
        Schema::drop($storage_name);        
    }
    
    private function createField($schema, string $name, string $type, $additional = null)
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
                $table_field = $schema->boolean($name);
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
    
    private function addFieldToSchema($schema, string $name, string|\stdClass $field)
    {
        $table_field = $this->createField($schema, $name, $field->type, isset($field->max_length)?$field->max_length:null);
        if (isset($field->default)) {
            $table_field->default($field->default);
        }
        if (isset($field->nullable)) {
            $table_field->nullable();
        }
        return $table_field;
    }
    
    /**
     * Helper function to determine if a storage description refers to an array or an object
     * 
     * @param \stdClass $info
     * @return bool
     */
    private function isArray(\stdClass $info): bool
    {
        return (isset($info->type) && ($info->type == 'array'));
    }
    
    /**
     * This created an array storage. Index could be integer or string and the lement type could
     * be any scalar
     * 
     * @param string $storage_name
     * @param \stdClass $info
     */
    private function createArray(string $storage_name, \stdClass $info)
    {
        Schema::create($storage_name, function($table) use ($info)
        {
            $table->integer('container_id');
            if ($info->index_type->type == 'integer') {
                $table->integer('index');
            } else {
                $table->string('index');
            }
            $this->createField($table, 'element', $info->element_type->type);
        });
    }
    
    /**
     * This creates an object storage by traversing the single elements and adding them to a schema
     * 
     * @param string $storage_name
     * @param \stdClass $info
     */
    private function createObjectTable(string $storage_name, \stdClass $info)
    {
        Schema::create($storage_name, function($table) use ($info)
        {
            foreach ($info as $name => $field_info) {
                $this->addFieldToSchema($table, $name, $field_info);
            }
            $table->primary('id');
        });        
    }
    
    /**
     * Whenever a storage is not existing (could be an object or an array storage) it has to be created freshly
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\AbstractObjectStorage::createStorage()
     */
    protected function createStorage(string $storage_name, $info)
    {
        if ($this->isArray($info)) {
            $this->createArray($storage_name, $info);
        } else {
            $this->createObjectTable($storage_name, $info);
        };        
    }
        
    private function alterArrayField(string $storage_name, string $name, \stdClass $from, \stdClass $to)
    {
        if ($name == 'index_type') {
            $name = 'index';
        } else if ($name == 'element_type') {
            $name = 'element';
        }
        Schema::table($storage_name, function($table) use ($name, $to)
        {            
            if (isset($to->type)) {
                $this->createField($table, $name, $to->type)->change();
            }
        });        
    }
    
    private function alterObjectField(string $storage_name, string $name, \stdClass $from, \stdClass $to)
    {
        Schema::table($storage_name, function($table) use ($name, $to)
        {
            
            if (isset($to->type)) {
                $this->createField($table, $name, $to->type)->change();
            }
        });        
    }
    
    private function alterField(string $storage_name, string $name, \stdClass $from, \stdClass $to)
    {
        if ($this->tableIsArray($storage_name)) {
            $this->alterArrayField($storage_name, $name, $from, $to);
        } else {
            $this->alterObjectField($storage_name, $name, $from, $to);
        }
    }
    
    private function dropField(string $storage_name, string $name)
    {
        Schema::dropColumns($storage_name, $name);        
    }
    
    private function addField(string $storage_name, string $name, \stdClass $descriptor)
    {
        Schema::table($storage_name, function($table) use ($name, $descriptor)
        {
            $this->addFieldToSchema($table, $name, $descriptor);
        });
    }
    
    protected function alterStorage(string $storage_name, $from, $to)
    {
        foreach ($from as $field => $descriptor) {
            if (isset($to->$field)) {
                $this->alterField($storage_name, $field, $descriptor, $to->$field);
            } else {
                $this->dropField($storage_name, $field);
            }
        }
        foreach ($to as $field => $descriptor) {
            if (!isset($from->$field)) {
                $this->addField($storage_name, $field, $descriptor);
            }
        }
    }
    
    protected function tableNeeded(string $name)
    {
        if (!Schema::hasTable($name)) {
            throw new StorageTableMissingException("The table '$name' is expected but missing.");
        }
    }
 
    public function getQueryExecutor(): Executor
    {
        return new MysqlQueryExecutor();
    }
    
}
