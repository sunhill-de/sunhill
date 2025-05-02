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
 * Coverage: 36.35% (2024-11-13)
 * PSR-State: completed
 */

namespace Sunhill\Storage\MysqlStorage;

use Sunhill\Storage\AbstractObjectStorage;
use Sunhill\Query\QueryParser\QueryNode;
use Illuminate\Support\Facades\Schema;
use Sunhill\Storage\Exceptions\StorageTableMissingException;
use Illuminate\Support\Facades\DB;

class MysqlObjectStorage extends AbstractObjectStorage
{

    /**
     * Updates the storage with the subid. It uses $key to identiy the record(s) and sets the givenvalues
     *
     * @param unknown $key
     * @param unknown $values
     */
    protected function updateStorageSubid(string $subid, int $key, array $values, string $key_field = 'id')
    {
    }
    
    /**
     * Deletes all references to key from the given
     *
     * @param unknown $key
     */
    protected function deleteStorageSubid(string $subid, int $key, string $key_field = 'id')
    {
        $this->tableNeeded($subid);
        DB::table($subid)->where($key_field,$key)->delete();
    }
    
    protected function insertObjects(array $values): int
    {
        $this->tableNeeded('objects');
        $id = DB::table('objects')->insertGetId($this->getObjectFields($values));
        $this->setID($id);
        return $id;
    }
    
    /**
     * Inserts into the given storage subid the given values. If value is a array of arrays then insert every entry as a separate record
     *
     * @param string $subid
     * @param array $values
     */
    protected function insertStorageSubid(string $subid, array $values)
    {
        $this->tableNeeded($subid);
        DB::table($subid)->insert($values);
    }
    
    /**
     * Loads from the given storage subif the values with the given key
     *
     * @param string $subid
     * @param int $key
     * @return array
     */
    protected function loadStorageSubid(string $subid, int $key, string $key_field = 'id'): array|\Traversable|\stdClass
    {
        $this->tableNeeded($subid);
        return DB::table($subid)->where($key_field, $key)->get();
    }
    
    protected function doExecuteQuery(QueryNode $node)
    {
    }

    protected function getCurrentStructure(string $storage_subid): \stdClass
    {
    }
    
    protected function patchStructure(\stdClass $diff)
    {
    }
    
    protected function tableNeeded(string $name)
    {
        if (!Schema::hasTable($name)) {
            throw new StorageTableMissingException("The table '$name' is expected but missing.");
        }
    }
    
    
    
}
