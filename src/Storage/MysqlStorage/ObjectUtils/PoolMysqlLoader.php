<?php
/**
 * @file PoolMysqlLoader.php
 * A helping class that isolates the loading procedure from the rest of the storage
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

use Illuminate\Support\Facades\DB;
use Sunhill\Tags\Tag;

class PoolMysqlLoader extends PoolMysqlUtility
{
        
    private function loadObject(int $id)
    {
        $this->tableNeeded('objects');
        return DB::table('objects')->where('id',$id)->first();
    }
    
    private function loadTable(string $name, int $id)
    {
        $this->tableNeeded($name);
        return DB::table($name)->where('id', $id)->first();    
    }
    
    private function loadArrays(int $id)
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
    
    private function loadTags(int $id)
    {
        $tags = DB::table('tagobjectassigns')->where('container_id', $id)->get();
        $result = [];
        foreach ($tags as $tag) {
            $result[] = new Tag($tag->tag_id);
        }
        return ['tags'=>$result];        
    }
    
    private function loadAttributes(int $id)
    {
        return [];    
    }
    
    public function load(int $id): ?array
    {
        if (is_null($object = $this->loadObject($id))) {
            return null;
        }
        $result = json_decode(json_encode($object),true);
        foreach ($this->getStorageSubids() as $subid) {
            if ($subid !== 'objects') {
                $result = array_merge($result, json_decode(json_encode($this->loadTable($subid, $id)),true));
            }
        }
        $result = array_merge($result, $this->loadArrays($id));        
        $result = array_merge($result, $this->loadTags($id));
        $result = array_merge($result, $this->loadAttributes($id));
        return $result;
    }
}