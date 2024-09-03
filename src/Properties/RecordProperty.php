<?php
/**
 * @file RecordProperty.php
 * Defines an property as base for all record like properties
 * Lang de,en
 * Reviewstatus: 2024-02-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: unknown
 */

namespace Sunhill\Properties\Properties;

use Sunhill\Properties\Properties\Exceptions\CantProcessPropertyException;
use Sunhill\Properties\Properties\Exceptions\DuplicateElementNameException;
use Sunhill\Properties\Properties\Exceptions\PropertyDoesntExistException;
use Sunhill\Properties\Facades\Properties;
use Sunhill\Properties\Storage\AbstractStorage;
use Sunhill\Properties\Properties\Exceptions\StorageAlreadySetException;
use Sunhill\Properties\Storage\StaticStorage;
use Ramsey\Collection\AbstractArray;

class RecordProperty extends AbstractRecordProperty
{
 
    /**
     * Stores the actual elements
     * @var array
     */
    protected $elements = [];
    
    /**
     * Clears the actual element list
     */
    protected function flushElements()
    {
        $this->elements = [];
    }
    
    /**
     * Sotres the action traits
     * @var array
     */
    protected $traits = [];
    
    /**
     * Clears the actual traits list
     */
    protected function flushTraits()
    {
        $this->traits = [];
    }
    
    protected function setItemsStorage(AbstractStorage $storage)
    {
        foreach ($this->elements as $element) {
            $element->setStorage($storage);
        }
    }
    
    protected function setTraitsStorage(AbstractStorage $storage)
    {
        foreach ($this->traits as $trait) {
            $trait->setStorage($storage);
        }
    }
    
    public function setStorage(AbstractStorage $storage)
    {
        parent::setStorage($storage);
        $this->setItemsStorage($storage);
        $this->setTraitsStorage($storage);
    }
    
    /**
     * Tries to process the given $element to a property. First it checks if it is a class name, then
     * it searches for a registered property with that name. 
     * 
     * @param string $element
     * @return AbstractProperty
     * @throws CantProcessPropertyException when the given element can't be processed to a property.
     */
    protected function processStringElement(string $element): AbstractProperty
    {
        if (class_exists($element)) {
            $return = new $element();
            if (!is_a($return, AbstractProperty::class)) {
                throw new CantProcessPropertyException("The given '$element' is not a property.");
            }            
        } else {
            if (!Properties::isPropertyRegistered($element)) {
                throw new CantProcessPropertyException("The given '$element' is not the name of a property.");
            }
            $element = Properties::getPropertyNamespace($element);
            $return = new $element();
        }
        return $return;
    }

    protected function doAddElement(string $name, AbstractProperty $element)
    {
        $element->setName($name); // Here we check if the name is valid 
        if (isset($this->elements[$name])) {
            throw new DuplicateElementNameException("The element name '$name' is already in use.");
        }
        $element->setOwner($this);
        if (!is_null($this->storage)) {
            $element->setStorage($this->storage);
        }
        $this->elements[$name] = $element;
    }

    private function getElementProperty($element): AbstractProperty
    {
        if (is_string($element)) {
            $element = $this->processStringElement($element);
        } else if (!is_a($element, AbstractProperty::class)) {
            if (is_scalar($element)) {
                throw new CantProcessPropertyException("Can't process '$element' to a property.");
            } else {
                throw new CantProcessPropertyException("Can't process the given parameter to a property.");
            }
        }
        return $element;
    }
    
    /**
     * Adds a new element to the list and returns this element
     * 
     * @param AbstractProperty|string $element
     * @return AbstractProperty
     */
    protected function addElement(string $name, $element): AbstractProperty
    {
        $element = $this->getElementProperty($element);
        $this->doAddElement($name, $element);
        return $element;    
    }
   
    /**
     * Adds a new element to the list and returns this element. 
     * For now a public alias for addElement()
     * @param string $name
     * @param unknown $element
     * @return AbstractProperty
     */
    public function appendElement(string $name, $element): AbstractProperty
    {
        return $this->addElement($name, $element);
    }
    
    private function doAddTrait($element)
    {
        $this->traits[] = $element;    
    }
    
    protected function addTrait($element): AbstractProperty
    {
        $element = $this->getElementProperty($element);
        $this->doAddTrait($element);
        return $element;
    }
   
    public function getOwningRecord(string $name)
    {
        if (isset($this->elements[$name])) {
            return $this;
        }
        foreach ($this->traits as $trait) {
            if ($owner = $trait->getOwningRecord($name)) {
                return $owner;
            }
        }
    }
    
    /**
     * constructor just calls initializeElements()
     */
    public function __construct()
    {
        $this->initializeElements();
    }
    
    protected function initializeElements()
    {
        
    }
        
// ************************ getElements ***********************************
    public function getElementNames()
    {
        $result = $this->getOwnElementNames();
        foreach ($this->traits as $trait) {
            $result = array_merge($result, $trait->getElementNames());
        }
        return $result;
    }
    
    public function getOwnElementNames()
    {
        return array_keys($this->elements);        
    }
    
    public function getElements()
    {
        $result = $this->getOwnElements();
        foreach ($this->traits as $trait) {
            $result = array_merge($result, $trait->getElements());
        }
        return $result;
    }
    
    public function getOwnElements()
    {
        return $this->elements;
    }
    
    public function getElementValues()
    {
        $result = $this->getOwnElementValues();
        foreach ($this->traits as $trait) {
            $result = array_merge($result, $trait->getElementValues());
        }
        return $result;    
    }
    
    public function getOwnElementValues()
    {
        return array_values($this->elements);
    }
    
    public function hasElement(string $name): bool
    {
        if (isset($this->elements[$name])) {
            return true;
        }
        foreach ($this->traits as $trait) {
            if ($trait->hasElement($name)) {
                return true;
            }
        }
        return false;    
    }
  
    protected function dispatchGetElement(AbstractProperty $element)
    {
        if (is_a($element, AbstractArrayProperty::class)) {
            return $element;
        }
        return $element->getValue();
    }
    
    protected function getTraitElement($trait, string $name)
    {
        return $trait->getElement($name);
    }
    
    public function getElement(string $name): AbstractProperty
    {
        if (isset($this->elements[$name])) {
            return $this->elements[$name];
        }
        foreach ($this->traits as $trait) {
            if ($trait->hasElement($name)) {
                return $this->getTraitElement($trait, $name);
            }
        }        
    }
        
// ***************************** Infomarket *******************************************
    public function static()
    {
        if (!is_null($this->storage)) {
            throw new StorageAlreadySetException('static() called and a storage was already set.');
        }
        $this->setStorage(new StaticStorage());
        return $this;
    }
}