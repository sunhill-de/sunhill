<?php
/**
 * @file AbstractRecordProperty.php
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

abstract class AbstractRecordProperty extends AbstractProperty implements \Iterator
{
     
    public function getAccessType(): string
    {
        return 'record';
    }
    
    /**
     * A direct assign to a record property is always invalid
     * {@inheritDoc}
     * @see Sunhill\\Properties\AbstractProperty::isValid()
     */
    public function isValid($input): bool
    {
        return false;
    }
    
// ****************************** Iterator **************************************
    protected $current = 0;
    
    public function current(): mixed
    {
        $elements = $this->getElements();
        return $this->getElements()[$this->key()];
    }
    
    public function key(): mixed
    {
        return $this->getElementNames()[$this->current];        
    }
    
    public function next(): void
    {
        $this->current++;
    }
    
    public function rewind(): void
    {
        $this->current = 0;
    }
    
    public function valid(): bool
    {
        return $this->current < count($this->getElements());
    }
    
// ************************ getElements ***********************************
    abstract public function getElementNames();
    
    abstract public function getElements();
    
    abstract public function getElementValues();
    
    abstract public function hasElement(string $name): bool;
    
    abstract public function getElement(string $name): AbstractProperty;
    
    
// ************************** transparent element handling *****************************
    protected function doGetValue()
    {
        return $this;
    }
    
    protected function handleUnkownRead(string $name)
    {
        return false;
    }
    
    protected function dispatchGetElement(AbstractProperty $element)
    {
        if (is_a($element, AbstractArrayProperty::class)) {
            return $element;
        }
        return $element->getValue();        
    }
    
    protected function getTraitValue($trait, string $name)
    {
        return $trait->$name;    
    }
    
    public function __get(string $name)
    {
        if (!$this->hasElement($name) && !$this->handleUnkownRead($name)) {            
            throw new PropertyDoesntExistException("The property '$name' doesnt exist.");
        }
        return $this->getElement($name)->getValue();
    }
    
    /**
     * Last opportunity to catch the write attempt to an unknown property
     * 
     * @param string $name
     * @param unknown $value
     * @return boolean
     */
    protected function handleUnkownWrite(string $name, $value)
    {
        return false;
    }
    
    public function __set(string $name, $value)
    {
        if (!$this->hasElement($name) && !$this->handleUnkownWrite($name, $value)) {
            throw new PropertyDoesntExistException("The property '$name' doesnt exist.");        
        }
        $this->getElement($name)->setValue($value);
    }
    
// ***************************** Infomarket *******************************************
    /**
     * Try to pass the request to a child element. If none is found return null
     * @param string $name
     * @param array $path
     * @return NULL
     */
    protected function passItemRequest(string $name, array $path)
    {
        if ($this->hasElement($name)) {
            return $this->getElement($name)->requestItem($path);
        }
        return parent::passItemRequest($name, $path);
    }
    
}