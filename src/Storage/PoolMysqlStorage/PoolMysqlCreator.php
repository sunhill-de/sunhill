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

namespace Sunhill\Storage\PoolMysqlStorage;

use Illuminate\Support\Facades\DB;
use Sunhill\Tags\Tag;

class PoolMysqlCreator extends PoolMysqlUtility
{
        
    private function createObject(int $id, array $values)
    {
        $this->tableNeeded('objects');
        return DB::table('objects')->where('id',$id)->first();
    }
    
    private function createTable(string $name, int $id, array $values)
    {
        $this->tableNeeded($name);
        return DB::table($name)->where('id', $id)->first();    
    }
    
    private function createArrays(int $id, array $values)
    {
        $result = [];
        foreach ($this->getArrays() as $array) {
            $table = $this->assembleArrayTableName($array);
            $this->tableNeeded($table);
            $table_result = DB::table($table)->where('container_id',$id)->get();
            $subresult = [];
            foreach ($table_result as $entry) {
                $subresult[$entry->index] = $entry->element;
            }
            $result[$array->name] = $subresult;
        }

        return $result;        
    }
    
    private function createTags(int $id, array $values)
    {
        $tags = DB::table('tagobjectassigns')->where('container_id', $id)->get();
        $result = [];
        foreach ($tags as $tag) {
            $result[] = new Tag($tag->tag_id);
        }
        return ['tags'=>$result];        
    }
    
    private function createAttributes(int $id, array $values)
    {
        return [];    
    }
    
    public function create(array $values): bool
    {
        $id = $this->createObject($values);
        foreach ($this->getStorageSubids() as $subid) {
            if ($subid !== 'objects') {
                $this->createTable($subid, $id,$values);
            }
        }
        $this->createArrays($id, $values);
        $this->createTags($id, $values);
        $this->createAttributed($id, $values);
        return true;
    }
}