<?php
/**
 * @file AttributeManager.php
 * Provides the AttributeManager object for accessing information about attributes.
 *
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-12-11
 * Localization: unknown
 * Documentation:
 * Wiki:  
 * Tests: Unit/Managers/AttributeManagerTest.php
 * Coverage: 
 */

namespace Sunhill\Managers;

use Sunhill\Basic\Base;
use Sunhill\Query\BasicQuery;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Storage\MysqlStorages\MysqlAttributeStorage;
use Sunhill\Managers\Exceptions\StorageSystemNotFoundException;

class AttributeManager extends Base
{
    
    protected $default_storage_system = 'mysql';
    
    /**
     * Return the id string of the default storage system for attributes (at the moment only mysql)
     * 
     * @return string
     */
    public function getDefaultStorageSystem(): string
    {
        return $this->default_storage_system;
    }
    
    /**
     * Creates an instance of the default storage system for attributes
     * 
     * @param string $name
     * @return AbstractStorage
     */
    public function getStorageSystem(string $name = ''): AbstractStorage
    {
        if (empty($name)) {
            $name = $this->getDefaultStorageSystem();   
        }
        switch ($name) {
            case 'mysql':
                return new MysqlAttributeStorage();
            default:
                throw new StorageSystemNotFoundException("The storage system '$name' is not known");
        }
    }
    
    /**
     * Returns a query for attributes
     * 
     * @param ?string $attribute_name (either the name of a attribute or (if null) a query on attributes at all)
     * @return BasicQuery
     */
    public function query(?string $attribute_name = null): BasicQuery
    {
        return $this->getStorageSystem()->query($attribute_name);        
    }
    
    public function registerAttribute($attribute)
    {
        
    }
    
}