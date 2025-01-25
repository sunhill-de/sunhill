<?php
/**
 * @file PoolMysqlCreator.php
 * A helping class that isolates the creation procedure from the rest of the storage
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-11-15
 * Localization: none
 * Documentation: unknown
 * Tests: /Unit/Storage/PoolMysqlStorage/AppendTest.php
 * Coverage: 
 * PSR-State: completed
 */

namespace Sunhill\Storage\MysqlStorages\ObjectUtils;

use Illuminate\Support\Facades\DB;
use Sunhill\Tags\Tag;

class PoolMysqlCreator extends PoolMysqlUtility
{
        
    private function createObject(array $values): int
    {
        $this->tableNeeded('objects');
        $id = DB::table('objects')->insertGetId($this->getObjectFields($values));
        return $id;
    }
    
    private function createTable(string $name, int $id, array $values)
    {
        $this->tableNeeded($name);
        $fields = $this->getTableFields($name, $values);
        $fields['id'] = $id;
        DB::table($name)->insert($fields);    
    }
    
    private function createArrays(int $id, array $values)
    {
        foreach ($this->getArrays() as $array) {
            if (!isset($values[$array->name])) {
                continue; // Arrays could be empty
            }
            $table = $this->assembleArrayTableName($array);
            $this->tableNeeded($table);
            $dataset = [];
            foreach ($values[$array->name] as $index => $value) {
                $dataset[] = ['container_id'=>$id,'index'=>$index,'element'=>$value];
            }
            DB::table($table)->insert($dataset);
        }
    }
    
    private function createTags(int $id, array $values)
    {
        $dataset = [];
        foreach ($values['tags']??[] as $tag) {
            $dataset[] = ['container_id'=>$id,'tag_id'=>$tag->getID()];
        }
        DB::table('tagobjectassigns')->insert($dataset);
    }
    
    private function createAttributes(int $id, array $values)
    {
        return [];    
    }
    
    public function create(array $values): int
    {
        $id = $this->createObject($values);
        foreach ($this->getStorageSubids() as $subid) {
            if ($subid !== 'objects') {
                $this->createTable($subid, $id, $values);
            }
        }
        $this->createArrays($id, $values);
        $this->createTags($id, $values);
        $this->createAttributes($id, $values);
        return $id;
    }
}