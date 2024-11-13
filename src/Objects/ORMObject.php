<?php
/**
 * @file ORMObject.php
 * Defines the basic class for storable record. Usually they are stored in a database
 * Lang en
 * Reviewstatus: 2024-11-13
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: 
 *
 * Wiki: 
 */

namespace Sunhill\Objects;

use Sunhill\Properties\PooledRecordProperty;
use Sunhill\Storage\PoolMysqlStorage\PoolMysqlStorage;
use Sunhill\Storage\AbstractStorage;

/**
 * The basic class for default storable records (in this case objects)
 * @author klaus
 *
 */
class ORMObject extends PooledRecordProperty
{
    
    protected static $inherited_inclusion = 'embed';
    
    protected function createStorage(): ?AbstractStorage
    {
        $storage = new PoolMysqlStorage();
        $storage->setStructure($this->getStructure()->elements);
        return $storage;
    }
    
}