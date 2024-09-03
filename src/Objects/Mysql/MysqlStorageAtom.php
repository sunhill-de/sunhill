<?php

namespace Sunhill\Properties\Objects\Mysql;

use Sunhill\Properties\Objects\AbstractStorageAtom;
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
    
}