<?php
/**
 * @file ArrayProperty.php
 * Defines an abstract property as base for all array like properties
 * Lang de,en
 * Reviewstatus: 2024-10-07
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: 89.81% (2024-11-13)
 * 
 * Wiki: /Array_properties
 */

namespace Sunhill\Properties;

use Sunhill\Properties\Exceptions\InvalidParameterException;
use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\InvalidValueException;
use Sunhill\Properties\Exceptions\InvalidIndexTypeException;
use Sunhill\Properties\Exceptions\InvalidIndexException;

class ArrayProperty extends AbstractProperty implements \ArrayAccess,\Countable,\Iterator
{
     
    protected $allowed_element_type = '';
    
    protected $shadow_element;

    /**
     * A instance of a property class was passed. So use this class
     * 
     * @param unknown $class
     * @return string
     */
    private function setAllowedElementType_class($class): string
    {
        return $class::class;        
    }
    
    /**
     * A string was passed. Could be a fully qualified class name or a internal class name
     * 
     * @param unknown $name
     * @return string
     */
    private function setAllowedElementType_name($name): string
    {
        if (class_exists($name)) {
            return $name;
        }
        if (!Properties::isPropertyRegistered($name)) {
            throw new InvalidParameterException("'$name' is not a property name.");
        }
        return Properties::getNamespaceOfProperty($name);
    }
    
    /**
     * Checks a passed parameter if it is valid for an array
     * 
     * @param unknown $type
     * @return string
     */
    private function checkElementType($type): string
    {
        if (is_string($type)) {
            return $this->setAllowedElementType_name($type);
        }
        if (is_a($type, AbstractProperty::class, true)) {
            return $this->setAllowedElementType_class($type);
        }
        throw new InvalidParameterException(getScalarMessage("The passed parameter :variable could not be processed to a property", $type));
    }
    
    /**
     * Stores the allowed element type and creates a shadow element for validity checks 
     * @param string $element_property
     */
    private function doSetElementType(string $element_property)
    {
        if (empty($element_property)) {
            return;
        }
        // For now there are no array of arrays!
        if (is_a($element_property,ArrayProperty::class,true)) {
            throw new InvalidParameterException("An array is not allowed as a element type");            
        }
        $this->allowed_element_type = $element_property;
        $this->shadow_element = new $element_property();
    }
    
    /**
     * Sets the allowed element type for this array
     * 
     * @param unknown $type_or_types
     * when $type is a string and the full qualified name of a property class then this is the allowed data Type
     * when $type is a string and the name of a property then this is the allowed data_type 
     * @return self
     * 
     * wiki: /Array_properties#Allowed_element_types
     */
    public function setAllowedElementType($type): self
    {
        if (!empty($type)) {
            $this->doSetElementType($this->checkElementType($type));
        }
        return $this;
    }
 
    /**
     * Returns an array of the current set allowed element types
     * 
     * @return array
     * 
     * wiki: /Array_properties#Allowed_element_types
     */
    public function getAllowedElementType(): string
    {
        return $this->allowed_element_type;    
    }
    
    protected function doOffsetSet(mixed $offset, mixed $value): void
    {
        $this->getStorage()->setIndexedValue($this->getName(), $offset, $this->formatForStorage($this->formatFromInput($value)));        
    }
    
    /**
     * Checks if a passed element is allowed as an element for this array
     * 
     * @param unknown $value
     * @return bool
     */
    public function checkElement($value): bool
    {
        if (empty($this->shadow_element)) {
            return true; // everything is allowed
        }
        return $this->shadow_element->isValid($value);
    }
    
    protected $index_type = 'integer';
    
    /**
     * Sets the index type for this array
     * 
     * @param string $index_type
     * @return static
     * 
     * @wiki /Array_properties#Indextypes_and_indices
     */
    public function setIndexType(string $index_type): static
    {
        $index_type = strtolower($index_type);
        if (!in_array($index_type,['integer','string'])) {
            throw new InvalidIndexTypeException("The index type '$index_type' is not allowed");
        }
        $this->index_type = $index_type;
        return $this;
    }
    
    /**
     * Returns the index type of this array
     * 
     * @return string
     * 
     * @wiki /Array_properties#Indextypes_and_indices
     */
    public function getIndexType(): string
    {
        return $this->index_type;
    }
    
    /**
     * returns if the given index exists
     * 
     * @param unknown $index
     * @return bool
     * 
     * @wiki /Array_properties#Indextypes_and_indices
     */
    public function indexExists($index): bool
    {
        $this->checkOffset($index);
    }
    
    private function isValidOffset(mixed $offset): bool
    {
        switch ($this->index_type) {
            case 'integer':
                if (is_int($offset) || is_null($offset)) {
                    return true;
                }
                break;
            case 'string':
                if (is_string($offset)) {
                    return true;
                }
                break;
        }
        return false;
    }
    
    protected function checkOffset(mixed $offset)
    {
        if ($this->isValidOffset($offset)) {
            return;
        }
        throw new InvalidIndexException("The given index is invalid");
    }
    
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->checkOffset($offset);
        if (!$this->checkElement($value)) {
            if (is_scalar($value)) {
                throw new InvalidValueException("The passed value '$value' is not an allowed element type for this array.");
            } else {
                throw new InvalidValueException("The passed value is not an allowed element type for this array.");
            }
        }
        $this->doOffsetSet($offset, $value);
    }
     
    protected function doOffsetGet(mixed $offset): mixed
    {
        if (!$this->getStorage()->getIsInitialized($this->getName())) {
            $this->handleUninitialized();
        }
        if (!$this->offsetExists($offset)) {
            throw new InvalidIndexException("The given index '$offset' is not defined.");
        }
        return $this->formatFromStorage($this->getStorage()->getIndexedValue($this->getName(),$offset));
    }
    
    public function unset(mixed $index)
    {
        return $this->offsetUnset($index);
    }
    
    public function offsetGet(mixed $offset): mixed
    {
        $this->checkOffset($offset);
        return $this->doOffsetget($offset);
    }
    
    public function count(): int
    {
        if (!$this->getStorage()->getIsInitialized($this->getName())) {
            $this->handleUninitialized();
        } 
        return $this->getStorage()->getElementCount($this->getName());
    }
    
    /**
     * Returns, if the given offset exists. Passes the request to the storage
     * {@inheritDoc}
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists(mixed $offset): bool
    {
        $this->checkForStorage('offsetExists');
        $this->checkOffset($offset);
        return $this->getStorage()->getOffsetExists($this->getName(),$offset);
    }
    
    public function offsetUnset(mixed $offset): void
    {
        $this->checkForStorage('offsetExists');
        $this->checkOffset($offset);
        $this->getStorage()->unsetOffset($this->getName(), $offset);
    }
    
    public function isValid($test): bool
    {
        if (is_null($test)) {
            return parent::isValid($test);
        }
        if (!is_array($test) && !($test instanceof \Traversable)) {
            return false;
        }
        foreach ($test as $key => $element) {
            if (!$this->isValidOffset($key)) {
                return false;
            }
            if (!$this->checkElement($element)) {
                return false;
            }
        }
        
        return true;
    }
        
    /**
     * Overwritten function to test, if an array like structure is assigned
     * 
     * {@inheritDoc}
     * @see \Sunhill\Properties\AbstractProperty::setValue()
     */
    public function setValue($value)
    {
        if (is_array($value) || ($value instanceof \Traversable)) {
            $result = [];
            foreach ($value as $key => $element) {
                $result[$key] = $element;   
            }
            parent::setValue($result);
        } else {
            parent::setValue($value);
        }
    }
    
    public static function getAccessType(): string
    {
        return 'array';
    }
    
    protected $current = 0;
    
    protected $keys;
    
    public function current(): mixed
    {
        return $this->getStorage()->getIndexedValue($this->getName(), $this->keys[$this->current]);
    }
    
    
    public function key(): mixed
    {
        return $this->keys[$this->current];
    }
    
    public function next(): void
    {
        $this->current++;
    }
    
    public function rewind(): void
    {
        $this->checkForStorage('traverse');
        if (!$this->getStorage()->getIsInitialized($this->getName())) {
            $this->handleUninitialized();
        }
        $this->current = 0;
        $this->keys = $this->getStorage()->getIndices($this->getName());
    }
    
    public function valid(): bool
    {
        return (($this->current >= 0) && ($this->current < $this->count()));
    }
    
    private function getElementType(): string
    {
        return $this->getAllowedElementType()::getStorageType();
    }
    
    public function getStructure(): \stdClass
    {
        $result = new \stdClass();
        $result->name = static::getName();
        $result->type = static::getAccessType();
        $result->element_type = $this->getElementType();
        $result->index_type = $this->getIndexType();        
        return $result;
    }

    protected static function setupInfos()
    {
        static::addInfo('name', 'array');
        static::addInfo('description', 'A class for arrays.', true);
    }
    
}