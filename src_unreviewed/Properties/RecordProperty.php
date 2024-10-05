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

namespace Sunhill\Properties;

use Sunhill\Properties\Exceptions\CantProcessPropertyException;
use Sunhill\Properties\Exceptions\DuplicateElementNameException;
use Sunhill\Properties\Exceptions\PropertyDoesntExistException;
use Sunhill\Facades\Properties;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Properties\Exceptions\StorageAlreadySetException;
use Sunhill\Storage\StaticStorage;
use Ramsey\Collection\AbstractArray;

class RecordProperty extends AbstractProperty implements \Iterator
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
    
    public function __construct(?callable $callback = null)
    {
        parent::__construct();
        
        $builder = new ElementBuilder($this);
        if (!is_null($callback)) {
            $callback($builder);
        }
        $this->initializeRecord($builder);
    }
    
    protected function initializeRecord(ElementBuilder $builder)
    {
        
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
    public function appendElement(AbstractProperty $property, string $name, string $inclusion = 'include'): AbstractProperty
    {
        return $this->addElement($property, $name);
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