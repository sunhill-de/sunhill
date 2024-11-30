<?php
/**
 * @file PoolMysqlUpdateMigrator.php
 * A helping class that isolates the Update migration of a storage
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-11-21
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: 100 % (2024-11-13)
 * PSR-State: completed
 */

namespace Sunhill\Storage\PoolMysqlStorage;

use Illuminate\Support\Facades\DB;
use Sunhill\Tags\Tag;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\InvalidTypeException;

class PoolMysqlUpdateMigrator extends PoolMysqlUtility
{
        
    private function migrateTable(string $name)
    {
        if (!DBTableExists($name)) {
            $fields = $this->getFieldsOf($name);
            Schema::create($name, function($table) use ($fields) 
            {
               $table->integer('id');
               foreach ($fields as $field) {
                   switch ($field->type) {
                       case 'integer':
                           $table_field = $table->integer($field->name);
                           break;
                       case 'string':
                           if (isset($field->max_length)) {
                               $table_field = $table->string($field->name, $field->max_length);
                           } else {
                               $table_field = $table->string($field->name);                               
                           }
                           break;
                       case 'text':
                           $table_field = $table->text($field->name);
                           break;
                       case 'date':
                           $table_field = $table->date($field->name);
                           break;
                       case 'time':
                           $table_field = $table->time($field->name);
                           break;
                       case 'datetime':
                           $table_field = $table->datetime($field->name);
                           break;
                       case 'float':
                           $table_field = $table->float($field->name);
                           break;
                       case 'boolean':
                           $table_field = $table->bool($field->name);
                           break;
                       case 'record':
                           $table_field = $table->text($field->name);
                           break;
                       case 'array':
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
               $table->primary('id');
            });
        }
    }
    
    private function getElementType(string $type)
    {
        return $type;    
    }
    
    private function migrateArrays()
    {
        foreach ($this->getArrays() as $array) {
            $table = $this->assembleArrayTableName($array);
            if (!DBTableExists($table)) {
                $index_type = $this->structure[$table]->index_type;
                $element_type = $this->getElementType($this->structure[$table]->element_type);
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
        }
    }
    
    /**
     * Deletes the given columns in table $table
     * 
     * @param string $table
     * @param array $drop_columns
     */
    private function dropColumns(string $table, array $drop_columns)
    {
        Schema::dropColumns($table, $drop_columns);
    }
    
    /**
     * Adds the previously not existing columns
     * 
     * @param string $table
     * @param array $new_columns
     */
    private function addColumns(string $table, array $new_columns)
    {
        Schema::table($table, function($schema) use ($new_columns) 
        {
            foreach ($new_columns as $column) {
                $this->addFieldToSchema($schema, $column);
            }
        });
    }
    
    private function changeColumns(string $table, array $changed_columns)
    {
        
    }
    
    private function addArrays(array $new_arrays)
    {
        foreach ($new_arrays as $array) {
            $table_name = $this->assembleArrayTableName($array);
            $this->addArray($table_name, $array->index_type, $array->element_type);
        }
    }
    
    /**
     * Is executed when there is something to do
     * 
     * @param unknown $changes
     * @return bool
     */
    public function migrate($changes): bool
    {
        foreach ($changes as $table => $migration_steps) {
            // Any dropped columns ?
            if (isset($migration_steps['dropped'])) {
                $this->dropColumns($table, $migration_steps['dropped']);
            }
            if (isset($migration_steps['new'])) {
                $this->addColumns($table, $migration_steps['new']);
            }
            if (isset($migration_steps['changed'])) {
                $this->changeColumns($table, $migration_steps['changed']);
            }
            if (isset($migration_steps['new_array'])) {
                $this->addArrays($migration_steps['new_array']);
            }
        }
        return true;
    }
    
    /**
     * Gets the columns that are no longe defined (to drop) for table $table
     * 
     * @param string $table
     * @return unknown[] or null (if none)
     */
    private function getDroppedColumns(string $table)
    {
        $result = [];
        
        $columns = Schema::getColumnListing($table);
        foreach ($columns as $column) {
            if ($column == 'id') {
                continue;
            }
            if (!isset($this->structure[$column])) {
                $result[] = $column;
            }
        }
        if (!empty($result)) {
            return $result;
        }
    }
    
    private function addIfNotNull(array &$array, string $key, $test)
    {
        if (!empty($test)) {
            $array[$key] = $test;
        }
    }
    
    /**
     * Checks all non array columns if something changed
     * 
     * @param string $table
     * @param array $result
     */
    private function checkColumns(string $table, array &$result)
    {
        $new = [];
        $changed = [];
        foreach ($this->getFieldsOf($table) as $field) {
            if ($field->type == 'array') {
                continue;
            }
            // Is this column in the table at all?
            if (!DBTableHasColumn($table, $field->name)) {
                $new[] = $field;
            } else if ($field->type !== DBTableColumnType($table, $field->name)) {
                $changed[] = $field->name;
            } // @todo Add structure change test here            
        }
        
        $this->addIfNotNull($result, 'new', $new);
        $this->addIfNotNull($result, 'changed', $changed);
    }

    private function checkDroppedArray(string $table)
    {
        $result = [];
        foreach (DB::connection()->getSchemaBuilder()->getTables() as $db_table) {
            if (str_starts_with($db_table['name'], $table.'_')) {
                $field_name = substr($db_table['name'],strlen($table+1));
              if (!isset($this->structure[$field_name])) {
                  $result[] = $field_name;
              }
            }
        }
        if (!empty($result)) {
            return $result;
        }
    }
    
    /**
     * Checks if there are any changes to array (either new array columns, dropped array columns or changed array columns
     * 
     * @param string $table
     * @param array $result
     */
    private function checkArrays(string $table, array &$result)
    {
        foreach ($this->getArraysOf($table) as $array) {
            $table_name = $this->assembleArrayTableName($array);
            if (DBTableExists($table_name)) {
                
            } else {
                // Table doesn't exist yet, so add it     
                if (isset($result['new_array'])) {
                    $result['new_array'][] = $array;
                } else {
                    $result['new_array'] = [$array];                    
                }
            }
        }
    }
    
    /**
     * Checks for a single table if some changes have to be applied to the database
     * 
     * @param string $table
     * @return \Sunhill\Storage\PoolMysqlStorage\unknown[][]
     */
    private function tableMigrationDirty(string $table)
    {
        $result = [];
        
        if ($dropped_columns = $this->getDroppedColumns($table)) {
            $result['dropped'] = $dropped_columns;            
        }
        $this->checkColumns($table, $result);
        if ($dropped_arrays = $this->checkDroppedArray($table)) {
            $result['dropped_arrays'] = $dropped_arrays;
        }
        $this->checkArrays($table, $result);
        
        if (!empty($result)) {
            return $result;
        }
    }
    
    /**
     * Checks if there are any changes that have to be applied to the database
     * 
     * @return boolean|\Sunhill\Storage\PoolMysqlStorage\unknown[][][] false if nothing to change
     */
    public function migrationDirty()
    {
        $result = [];
        
        foreach ($this->getStorageSubids() as $subid) {
            if ($subid !== 'objects') {
                if ($table_result = $this->tableMigrationDirty($subid)) {
                    $result[$subid] = $table_result;
                }
            }
        }
        
        if (empty($result)) {
            return false;
        }
        return $result;
    }
}