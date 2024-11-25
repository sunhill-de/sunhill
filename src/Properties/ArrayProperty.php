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
     
    protected $allowed_element_types = [];
    
    private function setAllowedElementTypes_array($array): array
    {
        $result = [];
        
        foreach ($array as $element) {
            $result = array_merge($result, $this->checkElementType($element));
        }
        
        return $result;
    }
    
    private function setAllowedElementTypes_class($class): array
    {
        return [$class];        
    }
    
    private function setAllowedElementTypes_name($name): array
    {
        if (!Properties::isPropertyRegistered($name)) {
            throw new InvalidParameterException("'$name' is not a property name.");
        }
        return [Properties::getNamespaceOfProperty($name)];
    }
    
    private function checkElementType($type_or_types): array
    {
        if (is_array($type_or_types)) {
            return $this->setAllowedElementTypes_array($type_or_types);
        }
        if (is_a($type_or_types, AbstractProperty::class, true)) {
            return $this->setAllowedElementTypes_class($type_or_types);
        }
        if (is_string($type_or_types)) {
            return $this->setAllowedElementTypes_name($type_or_types);
        }
        if (is_scalar($type_or_types)) {
            throw new InvalidParameterException("The passed scalar parameter could not be processed.");
        } else {
            throw new InvalidParameterException("The passed non scalar parameter could not be processed.");
        }        
    }
    
    /**
     * Sets the allowed element type for this array
     * 
     * @param unknown $type_or_types
     * when $type_or_types is an array each element is an allowed property type for this array
     * when $type_or_types is a string and the full qualified name of a property class then this is the allowed data Type
     * when $type_or_types is a string and the name of a property then this is the allowed data_type 
     * @return self
     * 
     * wiki: /Array_properties#Allowed_element_types
     */
    public function setAllowedElementTypes($type_or_types): self
    {
        if (!empty($type_or_types)) {
            $this->allowed_element_types = array_merge($this->allowed_element_types, $this->checkElementType($type_or_types));
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
    public function getAllowedElementTypes(): array
    {
        return $this->allowed_element_types;    
    }
    
    protected function doOffsetSet(mixed $offset, mixed $value): void
    {
        $this->getStorage()->setIndexedValue($this->getName(), $offset, $this->formatForStorage($this->formatFromInput($value)));        
    }
    
    protected function checkElementAgainstAllowed($value, string $type)
    {
        if (is_a($value, $type)) {
            return true;
        }
        $tester = new $type();
        return $tester->isValid($value);
    }
    
    protected function checkElement($value): bool
    {
        if (empty($this->allowed_element_types)) {
            return true; // everything is allowed
        }
        foreach ($this->allowed_element_types as $allowed_type) {
            if ($this->checkElementAgainstAllowed($value, $allowed_type)) {
                return true;
            }
        }
        return false;
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
    
    public function getAccessType(): string
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
    
    public function getStructure()
    {
        $result = new \stdClass();
        $result->name = $this->getName();
        $result->type = $this->getAccessType();
        $result->element_type = $this->getAllowedElementTypes();
        $result->index_type = $this->getIndexType();        
        return $result;
    }

    protected static function setupInfos()
    {
        static::addInfo('name', 'array');
        static::addInfo('description', 'A class for arrays.', true);
    }
    
}