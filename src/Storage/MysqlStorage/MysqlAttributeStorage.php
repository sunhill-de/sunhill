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
    
    public function loadAttribute(int $container_id, int $attribute_id)
    {
        $attribute = DB::table('attributes')->where('id',$attribute_id)->firstOrFail();
        $result = new \stdClass();
        $result->name = $attribute->name;
        $result->type = $attribute->type;
        $result->value = DB::table($this->assembleStorageName($attribute->name))->first('value')->value;
        
        return $result;
    }
    public function searchAttribute(string $name): ?\stdClass
    {
        
    }

    
}
