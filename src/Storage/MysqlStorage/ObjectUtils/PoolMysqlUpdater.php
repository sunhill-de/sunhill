<?php
/**
 * @file PoolMysqlUpdater.php
 * A helping class that isolates the update procedure from the rest of the storage
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-11-20
 * Localization: none
 * Documentation: unknown
 * Tests: /Unit/Storage/PoolMysqlStorage/UpdateTest.php
 * Coverage: 
 * PSR-State: completed
 */

namespace Sunhill\Storage\MysqlStorage\ObjectUtils;

use Illuminate\Support\Facades\DB;
use Sunhill\Tags\Tag;

class PoolMysqlUpdater extends PoolMysqlUtility
{
        
    private function updateObject(int $id, array $values)
    {
        $this->tableNeeded('objects');
        $object_fields = $this->getObjectFields($values);
        if (empty($object_fields)) {
            return true;
        }
        DB::table('objects')->where('id', $id)->update($object_fields);
        return true;
    }
    
    private function updateTable(string $name, int $id, array $values)
    {
        $this->tableNeeded($name);
        $fields = $this->getTableFields($name, $values);
        if (!empty($fields)) {
           DB::table($name)->where('id',$id)->update($fields);
        }
    }
    
    private function updateArrays(int $id, array $values)
    {
        foreach ($this->getArrays() as $array) {
            $table = $this->assembleArrayTableName($array);
            $this->tableNeeded($table);
            $dataset = [];
            if (!isset($values[$array->name])) {
                continue;
            }
            DB::table($table)->where('container_id', $id)->delete();
            foreach ($values[$array->name]->new as $index => $value) {
                $dataset[] = ['container_id'=>$id,'index'=>$index,'element'=>$value];
            }
            DB::table($table)->insert($dataset);
        }
    }
    
    private function updateTags(int $id, array $values)
    {
    }
    
    private function updateAttributes(int $id, array $values)
    {
    }
    
    public function update(int $id, array $changed): bool
    {
        if (!$this->updateObject($id, $changed)) {
            return false;
        }
        foreach ($this->getStorageSubids() as $subid) {
            if ($subid !== 'objects') {
                $this->updateTable($subid, $id, $changed);
            }
        }
        $this->updateArrays($id, $changed);
        $this->updateTags($id, $changed);
        $this->updateAttributes($id, $changed);
        return true;
    }
}