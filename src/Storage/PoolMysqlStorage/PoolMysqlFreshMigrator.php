<?php
/**
 * @file PoolMysqlFreshMigrator.php
 * A helping class that isolates the fresh migration of a storage
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

use Illuminate\Support\Facades\Schema;

class PoolMysqlFreshMigrator extends PoolMysqlUtility
{
        
    private function migrateTable(string $name)
    {
        if (!DBTableExists($name)) {
            $fields = $this->getFieldsOf($name);
            Schema::create($name, function($table) use ($fields) 
            {
               $table->integer('id');
               foreach ($fields as $field) {
                    $this->addFieldToSchema($table, $field);
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
                $this->addArray($table, $index_type, $element_type);
            }
        }
    }
    
    public function migrate(): bool
    {
        foreach ($this->getStorageSubids() as $subid) {
            if ($subid !== 'objects') {
                $this->migrateTable($subid);
            }
        }
        $this->migrateArrays();        
        return true;
    }
}