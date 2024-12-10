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

namespace Sunhill\Storage\PoolMysqlStorage;

use Sunhill\Storage\Exceptions\IDNotFoundException;
use Sunhill\Query\BasicQuery;
use Sunhill\Storage\AttributeStorage;

class MysqlAttributeStorage extends AttributeStorage
{

    /**
     * Loads the record with id '$id' from database
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::doLoad()
     */
    protected function doLoad(mixed $id)
    {
    }
    
    /**
     * Deleted the record with id '$id'
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::doDelete()
     */
    protected function doDelete(mixed $id)
    {
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
}
