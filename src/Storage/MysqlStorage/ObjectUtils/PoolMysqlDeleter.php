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

class PoolMysqlDeleter extends PoolMysqlUtility
{
        
    private function deleteObject(int $id)
    {
        $this->tableNeeded('objects');
        if (!($object = DB::table('objects')->where('id',$id)->first())) {
            return false;
        }
        DB::table('objects')->where('id',$id)->delete();
        return true;
    }
    
    private function deleteTable(string $name, int $id)
    {
        $this->tableNeeded($name);
        return DB::table($name)->where('id', $id)->delete();    
    }
    
    private function deleteArrays(int $id)
    {
        $result = [];
        foreach ($this->getArrays() as $array) {
            $table = $this->assembleArrayTableName($array);
            $this->tableNeeded($table);
            DB::table($table)->where('container_id',$id)->delete();
        }
    }
    
    private function deleteTags(int $id)
    {
        DB::table('tagobjectassigns')->where('container_id', $id)->delete();
    }
    
    private function deleteAttributes(int $id)
    {
        return [];    
    }
    
    public function delete(int $id): bool
    {
        if (!$this->deleteObject($id)) {
            return false;
        }
        foreach ($this->getStorageSubids() as $subid) {
            if ($subid !== 'objects') {
                $this->deleteTable($subid, $id);
            }
        }
        $this->deleteArrays($id);        
        $this->deleteTags($id);
        $this->deleteAttributes($id);
        return true;
    }
}