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
    
    /**
     * Gets the object name. If the object doesn't define an own setupInfos method (not nice) the method
     * calculated the name out of the class name.
     * 
     * @return string
     */
    public static function getObjectName(): string
    {
        if (static::definesOwnMethod('setupInfos')) {
            return static::getInfo('name');
        } else {
            $reflect = new \ReflectionClass(static::class);
            return ucfirst(strtolower($reflect->getShortName()));
        }        
    }
    
    /**
     * Just returns the obligate storage_id defined in the info block
     * 
     * @return string
     */
    public static function getStorageID(): string
    {
        if (static::definesOwnMethod('setupInfos')) {
            return static::getInfo('storage_id');
        } else {
            return strtolower(static::getObjectName()).'s';
        }
    }
    
    /**
     * Each object and collection has to (or better should) define at least the following informations:
     * * name = an unique name that identifies this object
     * * description = a description of what the purpose of this object/collection is
     * * storage_id = the id of the storage (in this case normally the database table)
     * * initiable = a boolean that indicates if this object can be initiaten directly (true) or only as an ancestor (false)
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'Object');
        static::addInfo('description', 'The basic class for objects and collections.', true);
        static::addInfo('storage_id', 'objects');
        static::addInfo('initiable', false);
    }
        
}