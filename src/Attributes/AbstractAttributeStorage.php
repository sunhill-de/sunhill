<?php
/**
 * @file AbstractAttributeStorage.php
 * Provides the basic class for attributes
 * Lang en
 * Create Date: 2024-07-16
 * Review date: 2025-08-10
 * Localization: complete
 * Documentation: complete
 *
 * Tests: tests/Unit/Attributes/AbstractAttributeStorageTest.php
 * Coverage Unit: not coverable
 * Coverage Feature: not coverable
 */
namespace Sunhill\Attributes;

use Sunhill\Basic\Base;
use Sunhill\Attributes\Exceptions\AttributeNotFoundException;
use Sunhill\Attributes\Exceptions\AttributeNotAssignedException;

/** 
 * An abstract storage that is able to handle attributes and their values. The association between 
 * an object and an attribute is handled by the ObjectStorage.
 * @author klausi
 *
 */
abstract class AbstractAttributeStorage extends Base
{
    
    /**
     * Constructs the name of the storage for the given attribute
     * 
     * @param string $attribute_name
     * @return string
     */
    protected function assembleStorageName(string $attribute_name): string
    {
        return 'attr_'.$attribute_name;
    }
    
    /**
     * Searches for an attribute with the given criteria
     * 
     * @param array $criteria
     * @return \stdClass
     */
    abstract public function searchAttribute(array $criteria): ?\stdClass;
    
    /**
     * Loads the value for the object $object_id from the storage with the given name
     * 
     * @param string $attribute_storage
     * @param int $object_id
     * @return mixed
     */
    abstract protected function getAttributeValue(string $attribute_storage, int $object_id);
    
    /**
     * Loads an attribute descriptor. This is a stdclass with these fields:
     * - $desc->name The name of the attribute
     * - $desc->type The type of the attribute (a standard storage type)
     * - $desc->value The value of this attribute 
     * 
     * @param int $container_id
     * @param int $attribute_id
     */
    public function loadAttribute(int $container_id, int $attribute_id): \stdClass
    {
        if (is_null($descriptor = $this->searchAttribute(['id'=>$attribute_id]))) {
            throw new AttributeNotFoundException("The attribute with the id '$attribute_id' was not found.");
        }
        if (is_null($descriptor->value = $this->getAttributeValue($this->assembleStorageName($descriptor->name), $container_id)))
        {
            throw new AttributeNotAssignedException("The attribute '".$descriptor->name."' was not assigned to object with id $container_id");            
        }
        
        return $descriptor;
    }
    
    /**
     * Stores the value $value for the object $object_id into the storge $attribute_storage
     * 
     * @param string $attribute_storage
     * @param int $object_id
     * @param mixed $value
     */
    abstract protected function storeAttributeValue(string $attribute_storage, int $object_id, mixed $value);
    
    /**
     * Stores the attribute $id with value $value for object $object_id
     *  
     * @param int $attr_id
     * @param int $object_id
     * @param unknown $value
     */
    public function storeAttribute(int $attr_id, int $object_id, $value)
    {
        if (is_null($attribute = $this->searchAttribute(['id'=>$attr_id]))) {
            throw new AttributeNotFoundException("The attribute the the id '$attr_id' was not found.");
        }
        $this->storeAttributeValue($this->assembleStorageName($attribute->name), $object_id, $value);
    }

    /**
     * Executes the removes the value identified by $object_id from the attribute_storage
     * 
     * @param string $attribute_storage
     * @param int $object_id
     */
    abstract protected function unsetAttributeValue(string $attribute_storage, int $object_id);

    /**
     * Removed from the atttribute identified by $attr_id the valud assigned to $object_id
     * 
     * @param int $attr_id
     * @param int $object_id
     */
    public function unsetAttribute(int $attr_id, int $object_id)
    {
        if (is_null($attribute = $this->searchAttribute(['id'=>$attr_id]))) {
            throw new AttributeNotFoundException("The attribute the the id '$attr_id' was not found.");
        }
        $this->unsetAttributeValue($this->assembleStorageName($attribute->name), $object_id);
    }
}