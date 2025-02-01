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
use Illuminate\Support\Str;
use Sunhill\Semantics\UUID4;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Properties\RecordProperty;
use Sunhill\Semantics\Name;
use Sunhill\Types\TypeVarchar;
use Sunhill\Types\TypeDateTime;
use Sunhill\Query\BasicQuery;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;

/**
 * The basic class for default storable records (in this case objects)
 * @author klaus
 *
 */
class ORMObject extends PooledRecordProperty
{
    
    protected static $inherited_inclusion = 'embed';

    public function __construct(?callable $elements = null)
    {
        parent::__construct($elements);
        $this->forceElement(UUID4::class, '_uuid')->setMaxLen(40);
        $this->forceElement(Name::class, '_classname')->setMaxLen(40);
        $this->forceElement(TypeVarchar::class,'_read_cap')->setMaxLen(20);
        $this->forceElement(TypeVarchar::class,'_modify_cap')->setMaxLen(20);;
        $this->forceElement(TypeVarchar::class,'_delete_cap')->setMaxLen(20);;
        $this->forceElement(TypeDateTime::class,'_created_at');
        $this->forceElement(TypeDateTime::class,'_updated_at');
    }
    
    private function forceElement(string $class, string $name)
    {
        $element = new $class();
        $element->forceName($name);
        $this->appendElement($element, null, 'objects');
        return $element;
    }
    
    public function create()
    {
        parent::create();
        $this->_uuid = (string)Str::uuid();
        $this->_classname = static::getInfo('name');
    }
    
    private function updateTimesstamps()
    {
        if (!$this->getID()) {
            $this->_created_at = now();
        }
        $this->_updated_at = now();        
    }
    
    private function commitTags()
    {
        
    }
    
    private function commitAttributes()
    {
        
    }
    
    public function commit()
    {
        $this->updateTimesstamps();
        parent::commit();
        if (static::isTaggable()) {
            $this->commitTags();
        }
        if (static::isAttributable()) {
            $this->commitAttributes();
        }
    }
    
    private function loadTags(int $id)
    {
        
    }
    
    private function loadAttributes(int $id)
    {
        
    }
    
    public function load($id)
    {
        parent::load($id);
        if (static::isTaggable()) {
            $this->loadTags($id);
        }
        if (static::isAttributable()) {
            $this->loadAttributes($id);
        }
    }
    
    /**
     * Deletes all references of tags of this object
     * 
     * @param int $id
     */
    private function deleteTags(int $id)
    {
        
    }
    
    /**
     * Deletes all references of attributes of this object
     * 
     * @param int $id
     */
    private function deleteAttributes(int $id)
    {
        
    }
    
    /**
     * Extends the inherited method by deleting tags and attribute references
     * 
     * {@inheritDoc}
     * @see \Sunhill\Properties\PooledRecordProperty::delete()
     */
    public function delete($id)
    {
        parent::delete($id);    
        if (static::isTaggable()) {
            $this->deleteTags($id);
        }
        if (static::isAttributable()) {
            $this->deleteAttributes($id);
        }
    }
    
    protected function createStorage(): ?AbstractStorage
    {
        $storage = new MysqlObjectStorage();
        $storage->setStructure($this->getStructure());
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
     * Returns, if this object may be tagged (default false)
     * 
     * @return bool
     */
    public static function isTaggable(): bool
    {
        return static::getInfo('taggable', false);
    }
    
    /**
     * Returns, if it is allowed to add attributed to this object (default false)
     * 
     * @return bool
     */
    public static function isAttributable(): bool
    {
        return static::getInfo('attributable', false);        
    }
    
    /**
     * Each object and collection has to (or better should) define at least the following informations:
     * * name = an unique name that identifies this object
     * * description = a description of what the purpose of this object/collection is
     * * storage_id = the id of the storage (in this case normally the database table)
     * * initiable = a boolean that indicates if this object can be initiated directly (true) or only as an ancestor (false)
     * * taggable = a boolean that indicates if this object can be tagged (true) or not (false)
     * * taggable = a boolean that indicates if this object can be attributed (true) or not (false)
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'Object');
        static::addInfo('description', 'The basic class for objects and collections.', true);
        static::addInfo('storage_id', 'objects');
        static::addInfo('initiable', false);
        static::addInfo('taggable', false);
        static::addInfo('attributable', false);
    }
        
}