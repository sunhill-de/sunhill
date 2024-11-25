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

use Illuminate\Support\Facades\DB;
use Sunhill\Tags\Tag;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\InvalidTypeException;

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