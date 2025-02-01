<?php
/**
 * @file PoolMysqlMigrator.php
 * A helping class that isolates the migration of a mysql storage
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

namespace Sunhill\Storage\MysqlStorage\ObjectUtils;

use Illuminate\Support\Facades\DB;
use Sunhill\Tags\Tag;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\InvalidTypeException;
use Sunhill\Helpers\Matrix;

class PoolMysqlMigrator extends PoolMysqlUtility
{
        
    /**
     * Deletes the given columns in table $table
     * 
     * @param string $table
     * @param array $drop_columns
     */
    private function dropColumns(string $table, \stdClass $drop_columns)
    {
        Schema::dropColumns($table, array_keys((array)$drop_columns));
    }
    
    /**
     * Adds the previously not existing columns
     * 
     * @param string $table
     * @param array $new_columns
     */
    private function addColumns(string $table, \stdClass $new_columns)
    {
        Schema::table($table, function($schema) use ($new_columns) 
        {
            foreach ($new_columns as $name => $column) {
                $this->addFieldToSchema($schema, $column);
            }
        });
    }
        
    /**
     * Adds a single table
     * 
     * @param \stdClass $table
     */
    private function addTable(string $name, \stdClass $table_info)
    {
        Schema::create($name, function($table) use ($table_info)
        {
            foreach ($table_info as $field) {
                $this->addFieldToSchema($table, $field);
            }
        });
    }
    
    /**
     * Adds all tables that are marked to add
     * 
     * @param \stdClass $new
     */
    private function addTables(\stdClass $new)
    {
        foreach ($new as $name => $table) {
            $this->addTable($name, $table);
        }
    }
    
    /**
     * Drops a single table
     * 
     * @param \stdClass $table
     */
    private function dropTable(string $table)
    {
        Schema::drop($table);
    }
    
    /**
     * Drops all tables that are marked to drop
     * 
     * @param \stdClass $drop
     */
    private function dropTables(\stdClass $drop)
    {
        foreach ($drop as $name => $table) {
            $this->dropTable($name);
        }
    }
    
    private function changeField($schema, string $name, \stdClass $field)
    {
        if (isset($field->changed->type)) {
            $this->createField($schema, $name, $field->changed->type)->change();
        }
    }
    
    private function changeColumns(string $name, \stdClass $changes)
    {
        Schema::table($name, function($schema) use ($changes)
        {
            foreach ($changes as $name => $field) {
                $this->changeField($schema, $name, $field);
            }
        });    
    }
    
    private function changeTable(string $name, \stdClass $table)
    {
        if (isset($table->new)) {
            $this->addColumns($name, $table->new);
        }
        if (isset($table->dropped)) {
            $this->dropColumns($name, $table->dropped);            
        }
        if (isset($table->changed)) {
            $this->changeColumns($name, $table->changed); 
        }
    }
    
    private function changeTables(\stdClass $change)
    {
        foreach ($change as $name => $table) {
            $this->changeTable($name, $table);
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
        if (isset($changes->new)) {
            $this->addTables($changes->new);            
        }
        if (isset($changes->dropped)) {
            $this->dropTables($changes->dropped);
        }
        if (isset($changes->changed)) {
            $this->changeTables($changes->changed);
        }
        return true;
    }
    
    protected $diff;
    
    /**
     * Constructs a matrix of table and table-fields that represents the current state
     * 
     * @return \Sunhill\Helpers\Matrix
     */
    private function getCurrentMatrix()
    {
        $result = new Matrix();     
        
        foreach ($this->structure->elements as $name => $descriptor) {
            if ($descriptor->storage_subid == 'objects') {
                continue;
            }
            $result->setItem([$descriptor->storage_subid, 'id', 'name'], 'id');
            $result->setItem([$descriptor->storage_subid, 'id', 'type'], 'integer');
            if ($descriptor->type == 'array') {
                $table_name = $this->assembleArrayTableName($descriptor);
                $result->setItem([$table_name, 'container_id', 'name'], 'container_id');
                $result->setItem([$table_name, 'container_id', 'type'], 'integer');
                $result->setItem([$table_name, 'index', 'name'], 'index');
                $result->setItem([$table_name, 'index', 'type'], $descriptor->index_type);
                $result->setItem([$table_name, 'element', 'name'], 'element');
                $result->setItem([$table_name, 'element', 'type'], $descriptor->element_type);
            } else {
                $result->setItem([$descriptor->storage_subid, $name, 'name'], $name); 
                $result->setItem([$descriptor->storage_subid, $name, 'type'], $descriptor->type);
            }
        }
        
        return $result;
    }
    
    private function getTableMatrix(string $table, Matrix &$matrix)
    {
        foreach (Schema::getColumnListing($table) as $column) {
            $matrix->setItem([$table, $column, 'name'], $column);
            $matrix->setItem([$table, $column, 'type'], DBTableColumnType($table, $column));
        }        
    }
    
    private function getTableMatrices(string $prefix, Matrix &$matrix)
    {
        foreach (DB::connection()->getSchemaBuilder()->getTables() as $db_table) {
            if (str_starts_with($db_table['name'], $prefix)) {
                $this->getTableMatrix($db_table['name'], $matrix);
            }
        }
    }
    
    private function getDatabaseMatrix()
    {
        $result = new Matrix();
                
        $table_prefixes = $this->getStorageSubids();
        foreach ($table_prefixes as $prefix) {
            if ($prefix !== 'objects') {
                $this->getTableMatrices($prefix, $result);
            }
        }
        return $result;        
    }
    
    /**
     * Checks if there are any changes that have to be applied to the database
     * 
     * @return boolean|\Sunhill\Storage\PoolMysqlStorage\unknown[][][] false if nothing to change
     */
    public function migrationDirty()
    {
        $current = $this->getCurrentMatrix();
        $database = $this->getDatabaseMatrix();
        
        $this->diff = $current->diff($database);

        if (is_null($this->diff)) {
            return false;
        } 
        return $this->diff;
   }
}