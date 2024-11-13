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
    protected function doLoad(mixed $id)
    {
        $loader = new PoolMysqlLoader($this->structure);
        if (is_null($this->values = $loader->load($id))) {
            throw new IDNotFoundException("The id '$id' was not found.");
        }
    }
    
    protected function doDelete(mixed $id)
    {
    }
    
    protected function isValidID(mixed $id): bool
    {
        return is_int($id);
    }
    
    protected function doCommitLoaded()
    {
    }
    
    protected function doCommitNew()
    {
    }
    
    protected function doMigrateNew()
    {
    }
    
    protected function doMigrateUpdate()
    {
    }
    
    protected function isAlreadyMigrated(): bool
    {
    }
    
    protected function isMigrationUptodate(): bool
    {
    }
    
}
