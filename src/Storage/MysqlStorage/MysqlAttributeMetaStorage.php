<?php
/**
 * @file MysqlAttributeMetaStorage.php
 * The mysql that stores the association between objects and attributes
 * 
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-12-17
 * Localization: none
 * Documentation: 
 * Tests: /tests/Unit/Storage/MysqlStorage/AttributeMetaStorage/*
 * Coverage: 
 */

namespace Sunhill\Storage\MysqlStorage;

use Sunhill\Query\BasicQuery;
use Illuminate\Support\Facades\DB;
use Sunhill\Storage\PersistentPoolStorage;

class MysqlAttributeMetaStorage extends PersistentPoolStorage
{

    /**
     * Loads the record with id '$id' from database
     *
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::doLoad()
     */
    protected function doLoad(mixed $id)
    {
        $query = DB::table('attributeobjectassigbs')->where('container_id',$id)->get();
    }
    
    /**
     * Deleted the record with id '$id'
     *
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::doDelete()
     */
    protected function doDelete(mixed $id)
    {
        DB::table('attributeobjectassigns')->where('container_id', $id)->delete();
    }
    
    /**
     * Mysql database table expect integer as id
     *
     * @param mixed $id
     * @return bool
     */
    protected function isValidID(mixed $id): bool
    {
        return is_int($id);
    }
    
    /**
     * Updates the already stored record into the database
     *
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::doCommitLoaded()
     */
    protected function doCommitLoaded()
    {
    }
    
    /**
     * Writes a new record into the database and returns its id
     *
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::doCommitNew()
     */
    protected function doCommitNew()
    {
    }
    
    /**
     * Creates all table that belong to the given structure
     *
     * {@inheritDoc}
     * @see \Sunhill\Storage\AbstractPersistentStorage::doMigrateNew()
     */
    protected function doMigrateNew()
    {
    }
    
    protected $migrator;
    
    protected function doMigrateUpdate($info)
    {
    }
    
    protected function isAlreadyMigrated(): bool
    {
        return true;
    }
    
    protected function migrationDirty()
    {
    }
    
    protected function doQuery(): BasicQuery
    {
    }
    
    public function forceID(int $id): static
    {
        $this->setID($id);
        return $this;
    }
}
