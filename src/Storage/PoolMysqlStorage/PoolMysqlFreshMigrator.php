<?php
/**
 * @file PoolMysqlFreshMigrator.php
 * A helping class that isolates the fresh migration of a storage
 *
 * @author Klaus Dimde
 * Lang en
 * Reviewstatus: 2024-11-21
 * Localization: none
 * Documentation: unknown
 * Tests: unknown
 * Coverage: 100 % (2024-11-13)
 * PSR-State: completed
 */

namespace Sunhill\Storage\PoolMysqlStorage;

use Illuminate\Support\Facades\DB;
use Sunhill\Tags\Tag;

class PoolMysqlFreshMigrator extends PoolMysqlUtility
{
        
    private function migrateTable(string $name)
    {
        if (!DBTableExists($name)) {
            
        }
    }
    
    private function migrateArrays()
    {
        foreach ($this->getArrays() as $array) {
            $table = $this->assembleArrayTableName($array);
        }

    }
    
    public function migrate(): bool
    {
        foreach ($this->getStorageSubids() as $subid) {
            if ($subid !== 'objects') {
                $this->migrateTable($subid);
            }
        }
        $this->migrateArrays();        
    }
}