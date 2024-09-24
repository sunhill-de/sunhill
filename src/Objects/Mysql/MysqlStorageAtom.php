<?php

namespace Sunhill\Objects\Mysql;

use Sunhill\Objects\AbstractStorageAtom;
use Illuminate\Support\Facades\DB;

class MysqlStorageAtom extends AbstractStorageAtom
{
 
    protected function readItemsAsRecord($id): array
    {
        if (empty($result = DB::table($this->source)->where('id', $id)->first())) {
            $this->idNotFound($id);            
        }
        return $result;
    }
    
    protected function readItemsAsArray($id): array
    {
        return DB::table($this->source)->where('container_id', $id)->get();
    }
    
    protected function readItemsAsUUID($id): array
    {
        return DB::table($this->source)->where('uuid', $id)->first();        
    }
    
    protected function readItemsAsObject($id): array
    {
        return $this->readItemsAsRecord($id);
    }
    
    protected function handleRecord(string $storage_id, array $descriptor, $additional1 = null, $additional2 = null)
    {
        
    }
    
    protected function handleDirectory(string $storage_id, $additional = null)
    {
        
    }
    
    
    protected function handleTags($additional = null)
    {
        
    }
    
    
    protected function handleAttributes($additional = null)
    {
        
    }
    
    
    
}