<?php

/**
 * @file ORMObject.php
 * Defines the basic class for storable record. Usually they are stored in a database
 * Lang en
 * Reviewstatus: 2024-11-13
 * Localization: complete
 * Documentation: complete
 * Tests:
 * Coverage Unit: 85.39 % (2025-06-06)
 *
 * Wiki:
 */

namespace Sunhill\Objects;

use Illuminate\Support\Str;
use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\InvalidPropertyException;
use Sunhill\Properties\Exceptions\InvalidValueException;
use Sunhill\Properties\PooledRecordProperty;
use Sunhill\Semantics\Name;
use Sunhill\Semantics\UUID4;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Storage\MysqlStorage\MysqlObjectStorage;
use Sunhill\Tags\TagList;
use Sunhill\Types\TypeBoolean;
use Sunhill\Types\TypeDate;
use Sunhill\Types\TypeDateTime;
use Sunhill\Types\TypeFloat;
use Sunhill\Types\TypeInteger;
use Sunhill\Types\TypeText;
use Sunhill\Types\TypeTime;
use Sunhill\Types\TypeVarchar;

/**
 * The basic class for default storable records (in this case objects)
 *
 * @author klaus
 */
class ORMObject extends PooledRecordProperty
{
    protected $tag_list;

    protected static $inherited_inclusion = 'embed';

    public function __construct(?callable $elements = null)
    {
        parent::__construct($elements);
        $this->forceElement(UUID4::class, '_uuid')->setMaxLen(40);
        $this->forceElement(Name::class, '_classname')->setMaxLen(40);
        $this->forceElement(TypeVarchar::class, '_read_cap')->setMaxLen(20)->nullable()->default(null);
        $this->forceElement(TypeVarchar::class, '_modify_cap')->setMaxLen(20)->nullable()->default(null);
        $this->forceElement(TypeVarchar::class, '_delete_cap')->setMaxLen(20)->nullable()->default(null);
        $this->forceElement(TypeDateTime::class, '_created_at');
        $this->forceElement(TypeDateTime::class, '_updated_at');
        $this->initializeTagList();
        $this->initializeAttributes();
    }

    private function initializeTagList()
    {
        $storage = $this->getStorage();
        $this->tag_list = new TagList($storage);

    }

    private function initializeAttributes()
    {
        $storage = $this->getStorage();
        $storage->setValue('_attributes', []);
    }

    private function forceElement(string $class, string $name)
    {
        $element = new $class;
        $element->forceName($name);
        $this->appendElement($element, null, 'objects');

        return $element;
    }

    public function create()
    {
        parent::create();
        $storage = $this->getStorage();
        $storage->setStructure($this->getStructure()); // @todo Why is this neccessary??
        $storage->setValue('_attributes', []);
        $this->_uuid = (string) Str::uuid();
        $this->_classname = static::getInfo('name');

    }

    private function updateTimesstamps()
    {
        if (! $this->getID()) {
            $this->_created_at = now();
        }
        $this->_updated_at = now();
    }

    private function commitTags() {}

    private function commitAttributes() {}

    /**
     * This method is callen whenever the class defines no own properties (= skipping record)
     */
    protected function handleSkippingRecord(string $pointer)
    {
        if ($pointer::hasInfo('storage_id') && ($pointer::getInfo('storage_id') !== 'objects')) {
            $this->skipping_members[$pointer] = $pointer::getInfo('storage_id');
        }
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

    private function loadTags(AbstractStorage $storage, int $id)
    {
        $this->tag_list->setStorage($storage);
        $this->tag_list->loadFromStorage();
    }

    private function loadAttributes(int $id) {}

    public function load($id)
    {
        $storage = $this->getStorage();
        $stored_class = $storage->getClassOf($id);
        if (($stored_class !== '') && ($stored_class !== static::getInfo('name'))) {
            throw new InvalidPropertyException('Expected '.static::getInfo('name').' found '.$stored_class);
        }
        parent::load($id);
        if (static::isTaggable()) {
            $this->loadTags($storage, $id);
        }
        if (static::isAttributable()) {
            $this->loadAttributes($id);
        }
    }

    /**
     * Deletes all references of tags of this object
     */
    private function deleteTags(int $id) {}

    /**
     * Deletes all references of attributes of this object
     */
    private function deleteAttributes(int $id) {}

    /**
     * Extends the inherited method by deleting tags and attribute references
     *
     * {@inheritDoc}
     *
     * @see \Sunhill\Properties\PooledRecordProperty::delete()
     */
    public function delete($id = null)
    {
        $id = $id ?? $this->getID();
        $storage = $this->getStorage();
        $stored_class = $storage->getClassOf($id);
        if (($stored_class == '') || ($stored_class == static::getInfo('name'))) {
            parent::delete($id);
        } else {
            $object_class = Properties::getNamespaceOfProperty($stored_class);
            $object = new $object_class;
            $object->delete($id);
        }
        if (static::isTaggable()) {
            $this->deleteTags($id);
        }
        if (static::isAttributable()) {
            $this->deleteAttributes($id);
        }
    }

    protected function createStorage(): ?AbstractStorage
    {
        $storage = new MysqlObjectStorage;
        $storage->setStructure($this->getStructure());

        return $storage;
    }

    public function __get($varname): mixed
    {
        if ($varname == '_tags') {
            return $this->tag_list;
        }
        if (in_array($varname, $this->getStorage()->getValue('_attributes'))) {
            return $this->getStorage()->getIndexedValue('_attributes', $varname);
        }

        return parent::__get($varname);
    }

    private function validateAttribute(string $attribute_name, mixed $value)
    {
        switch (Properties::getAttributeType($attribute_name)) {
            case 'integer':
                $tester = new TypeInteger;
                break;
            case 'float':
                $tester = new TypeFloat;
                break;
            case 'string':
                $tester = new TypeVarchar;
                break;
            case 'date':
                $tester = new TypeDate;
                break;
            case 'time':
                $tester = new TypeTime;
                break;
            case 'datetime':
                $tester = new TypeDateTime;
                break;
            case 'boolean':
                $tester = new TypeBoolean;
                break;
            case 'text':
                $tester = new TypeText;
                break;
        }
        if (! $tester->isValid($value)) {
            throw new InvalidValueException('Invalid value assigned to attribute');
        }
    }

    private function storeAttribute(string $attribute_name, mixed $value)
    {
        $this->getStorage()->setIndexedValue('_attributes', $attribute_name, $value);
    }

    private function tryToHandleNewAttribute(string $attribute_name, mixed $value): bool
    {
        if ($attribute_id = Properties::getAttributeID($attribute_name)) {
            $this->validateAttribute($attribute_name, $value);
            $this->storeAttribute($attribute_name, $value);

            return true;
        }

        return false;
    }

    private function tryToHandleAttribute(string $attribute_name, mixed $value): bool
    {
        if ($this->hasElement($attribute_name)) {
            return false;
        }
        if (in_array($attribute_name, $this->getStorage()->getValue('_attributes'))) {
            $this->validateAttribute($attribute_name, $value);
            $this->storeAttribute($attribute_name, $value);

            return true;
        }

        return $this->tryToHandleNewAttribute($attribute_name, $value);
    }

    public function __set($varname, $value)
    {
        if ($this->tryToHandleAttribute($varname, $value)) {
            return;
        }
        parent::__set($varname, $value);
    }

    public function hasAttributes(): bool
    {
        return count($this->getStorage()->getValue('_attributes')) > 0;
    }

    protected static function getStorageClass(): string
    {
        return MysqlObjectStorage::class;
    }

    /**
     * Gets the object name. If the object doesn't define an own setupInfos method (not nice) the method
     * calculated the name out of the class name.
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
     */
    public static function isTaggable(): bool
    {
        return static::getInfo('taggable', false);
    }

    /**
     * Returns, if it is allowed to add attributed to this object (default false)
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
