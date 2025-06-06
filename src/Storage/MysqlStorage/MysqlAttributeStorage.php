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
 * Coverage Unit: 87.50 % (2025-06-06)
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

    protected function assembleStorageName(string $attribute_name): string
    {
        return 'attr_'.$attribute_name;    
    }
    
    /**
     * Loads the attribute identified by $attribute_id for the object identified by $container_id
     * 
     * {@inheritDoc}
     * @see \Sunhill\Attributes\AbstractAttributeStorage::loadAttribute()
     */
    public function loadAttribute(int $container_id, int $attribute_id)
    {
        $attribute = DB::table('attributes')->where('id',$attribute_id)->firstOrFail();
        $result = new \stdClass();
        $result->name = $attribute->name;
        $result->type = $attribute->type;
        $result->value = DB::table($this->assembleStorageName($attribute->name))->where('container_id',$container_id)->firstOrFail('value')->value;
        
        return $result;
    }
    
    /**
     * Searches for an attribute with the given name
     * 
     * {@inheritDoc}
     * @see \Sunhill\Attributes\AbstractAttributeStorage::searchAttribute()
     */
    public function searchAttribute(string $name): ?\stdClass
    {
        $attribute = DB::table('attributes')->where('name', $name)->first();
        if (empty($attribute)) {
            return null;
        }
        return $attribute;
    }

    /**
     * Stores the attribute identified by $attr_id for object $object_id with the $value $value
     * 
     * {@inheritDoc}
     * @see \Sunhill\Attributes\AbstractAttributeStorage::storeAttribute()
     */
    public function storeAttribute(int $attr_id, int $object_id, $value)
    {
        $attribute = DB::table('attributes')->where('id',$attr_id)->firstOrFail();
        DB::table($this->assembleStorageName($attribute->name))->upsert(['container_id'=>$object_id, 'value'=>$value],'container_id');
    }
    
}
