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

use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Storage\Exceptions\IDNotFoundException;

class PoolMysqlStorage extends PersistentPoolStorage
{

    /**
     * Loads the record with id '$id' from database
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::doLoad()
     */
    protected function doLoad(mixed $id)
    {
        $loader = new PoolMysqlLoader($this->structure);
        if (is_null($this->values = $loader->load($id))) {
            throw new IDNotFoundException("The id '$id' was not found.");
        }
    }
    
    /**
     * Deleted the record with id '$id'
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::doDelete()
     */
    protected function doDelete(mixed $id)
    {
        $deleter = new PoolMysqlDeleter($this->structure);
        if (!$deleter->delete($id)) {
            throw new IDNotFoundException("The id '$id' was not found.");            
        }
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
       $updater = new PoolMysqlUpdater($this->structure);
       $updater->update($this->getID(),$this->getModifiedValues());
    }
    
    /**
     * Writes a new record into the database and returns its id
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\PersistentPoolStorage::doCommitNew()
     */
    protected function doCommitNew()
    {
        $creator = new PoolMysqlCreator($this->structure);
        if (!($id = $creator->create($this->values))) {
            throw new IDNotFoundException("The id '$id' was not found.");            
        }
        return $id;
    }
    
    /**
     * Creates all table that belong to the given structure
     * 
     * {@inheritDoc}
     * @see \Sunhill\Storage\AbstractPersistentStorage::doMigrateNew()
     */
    protected function doMigrateNew()
    {
        $migrator = new PoolMysqlFreshMigrator($this->structure);
        $migrator->migrate();
    }

    protected $migrator;
    
    protected function doMigrateUpdate($info)
    {
        $this->migrator->migrate($info);
    }
    
    protected function getStorageSubids(): array
    {
        $result = [];
        foreach ($this->structure as $entry) {
            if (!in_array($entry->storage_subid,$result)) {
                $result[] = $entry->storage_subid;
            }
        }
        return $result;
    }
        
    protected function isAlreadyMigrated(): bool
    {
        $subids = $this->getStorageSubids();
        foreach ($subids as $subid) {
            if (!DBTableExists($subid)) {
                return false;
            }
        }
        return true;
    }
    
    protected function migrationDirty()
    {
        $this->migrator = new PoolMysqlUpdateMigrator($this->structure);
        return $this->migrator->migrationDirty();
    }
    
}
