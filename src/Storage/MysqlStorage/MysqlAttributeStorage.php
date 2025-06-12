<?php
/**
 * @file PoolMysqlStorage.php
 * A persistent pool storage that uses an mysql/mariadb backend as a persistent storage.
 * 
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-10-12
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage Unit: 87.50  (2025-06-06)
 * PSR-State: completed
 */

namespace Sunhill\Storage\MysqlStorage;

use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Query\BasicQuery;
use Illuminate\Support\Facades\DB;
use Sunhill\Storage\ObjectStorage\AttributeStorage;
use Sunhill\Attributes\AbstractAttributeStorage;
use Sunhill\Attributes\stdClass;

class MysqlAttributeStorage extends AbstractAttributeStorage
{

    /**
     * Loads the attribute of the given object from the given storage
     * 
     * {@inheritDoc}
     * @see \Sunhill\Attributes\AbstractAttributeStorage::getAttributeValue()
     */
    protected function getAttributeValue(string $attribute_storage, int $object_id)
    {
        $result = DB::table($attribute_storage)->where('container_id',$object_id)->first('value');
        if (is_null($result)) {
            return $result;
        }
        return $result->value;
    }
        
    /**
     * Searches for an attribute with the given name
     * 
     * {@inheritDoc}
     * @see \Sunhill\Attributes\AbstractAttributeStorage::searchAttribute()
     */
    public function searchAttribute(array $criteria): ?\stdClass
    {
        $query = DB::table('attributes');
        foreach ($criteria as $key => $value) {
            $query->where($key,'=',$value);
        }
        $attribute = $query->first();
        if (empty($attribute)) {
            return null;
        }
        return $attribute;
    }

    protected function storeAttributeValue(string $attribute_storage, int $object_id, mixed $value)
    {
        DB::table($attribute_storage)->upsert(['container_id'=>$object_id, 'value'=>$value],'container_id');        
    }
    
    protected function unsetAttributeValue(string $attribute_storage, int $object_id)
    {
        DB::table($attribute_storage)->where(['container_id'=>$object_id])->delete();
    }
    
    
}
