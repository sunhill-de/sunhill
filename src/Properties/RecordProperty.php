<?php
/**
 * @file RecordProperty.php
 * Defines a property as a base for all record like properties
 * Lang en
 * Reviewstatus: 2024-10-24
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: 
 *
 * Wiki: /RecordProperties
 * tests /tests/Unit/Properties/RecordProperties/*
 */

namespace Sunhill\Properties;

use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\NotAPropertyException;
use Sunhill\Properties\Exceptions\PropertyNameAlreadyGivenException;
use Sunhill\Properties\Exceptions\PropertyAlreadyInListException;
use Sunhill\Properties\Exceptions\PropertyHasNoNameException;
use Sunhill\Properties\Exceptions\InvalidInclusionException;
use Sunhill\Properties\Exceptions\NotAllowedInclusionException;
use Sunhill\Properties\Exceptions\PropertyNotFoundException;
use Sunhill\Storage\AbstractStorage;

class RecordProperty extends AbstractProperty implements \Countable,\Iterator
{
   
    /**
     * Stores how this record should treat inherited elements
     * 
     * @var string
     */
    protected static $inherted_inclusion = 'include';
        
    public function __construct($elements = null)
    {
        parent::__construct();
        $this->initializeInheritance();
        
        if (is_callable($elements)) {
            $this->setupRecord($elements);
        }
    }
    
    /**
     * Is called by
     *  a) the constructor when a callable $elements parameter is passed
     *  b) by initializeInheritance() for every ancestor
     *  c) directly by the initializing structure
     * @param callable $elements
     * @param RecordProperty $target
     */
    public function setupRecord(callable $elements,?RecordProperty $target = null): static
    {
        if (is_null($target)) {
            $target = $this;
        }
        
        return $this;
    }
    
    protected static function initializeRecord(ElementBuilder $builder)
    {
        
    }
    
    private function initializeInheritance()
    {
        
    }
    
    
    public function getAccessType(): string
    {
        return 'record';
    }

    public function isValid($input): bool
    {
        
    }
    
    /**
     * Overwrites the inherited setStorage method to pass the new storage
     * to all of the elements.
     * 
     * {@inheritDoc}
     * @see \Sunhill\Properties\AbstractProperty::setStorage()
     */
    public function setStorage(AbstractStorage $storage): static
    {
        parent::setStorage($storage);
        foreach ($this->elements as $key => $element) {
            $element->setStorage($storage);        
        }
        return $this;
    }
    
    /**
     * For storages it is sometimes necessary to know the storage id for this 
     * property (for example several database tables). This method returns a id
     * @return string
     */
    public function getStorageID(): string
    {
        return ''; // Per default nothing
    }
    
    /**
     * Storages the elements of this record as an associative array
     * @var array
     */
    protected array $elements = [];
    
    /**
     * Stores the structure of the elements of this elements as an associative array
     * @var array
     */
    protected array $elements_structure = [];
    
    /**
     * Tries to "translate" $element into a property
     * @param unknown $element
     * @return AbstractProperty
     */
    private function getElementProperty($element): AbstractProperty
    {
        if (is_string($element)) {
            if (class_exists($element)) {
                $element = new $element();                
            } else {
                $namespace = Properties::getNamespaceOfProperty($element);
                $element = new $namespace();
            }
        }
        if (is_a($element, AbstractProperty::class)) {
            return $element;
        }
        throw new NotAPropertyException("The given object is not a property");
    }
    
    /**
     * Checks if $name is set, if yes set it to the element
     * @param AbstractProperty $propery
     * @param string $name
     */
    private function writeName(AbstractProperty $property, ?string $name)
    {
        if (!empty($name)) {
            $property->setName($name);
        }
        if (empty($property->getName())) {
            throw new PropertyHasNoNameException("The property has no name");
        }
    }
    
    /**
     * Checks if the name is alread given
     * @param AbstractProperty $property
     */
    private function checkForDuplicateName(AbstractProperty $property)
    {
        if (array_key_exists($property->getName(), $this->elements)) {
            throw new PropertyNameAlreadyGivenException("The name '".$property->getName()."' is already in use");
        }
    }
    
    /**
     * Checks if the propeery was already appended
     * @param AbstractProperty $property
     */
    private function checkForDuplicateProperty(AbstractProperty $property)
    {
        if (in_array($property, array_values($this->elements))) {
            throw new PropertyAlreadyInListException("The property '".$property->getName()."' is already in this record");
        }
    }
    
    private function appendMemebers(RecordProperty $property, string $inclusion)
    {
        foreach ($property as $name => $element) {
            $this->checkForDuplicateName($element);
            $this->checkForDuplicateProperty($element);
            $this->elements[$name] = $element;
            $structure = $element->getStructure();
            if ($inclusion == 'include') {
                $structure->storage_subid = $this->getStorageID();
            } else {
                $structure->storage_subid = $property->getStorageID();                
            }
        }
    }
    
    /**
     * This is the default inclusion. And it's the only possible for non RecordProperty types. 
     * For those this method just adds the property to the list and to the structure list.
     *  
     * @param AbstractProperty $property
     */
    private function checkInclude(AbstractProperty $property)
    {
        if (!is_a($property,RecordProperty::class)) {
            $this->elements[$property->getName()] = $property;
            $this->elements_structure[$property->getName()] = $property->getStructure();
            return;            
        }
        $this->appendMemebers($property, 'include');
    }
    
    /**
     * This inclusion is only possible for record properties that are related to each other. 
     * To be more precicely: The property to embed has to an ancestor of the embedding one.
     *
     * @param AbstractProperty $property
     */
    private function checkEmbed(AbstractProperty $property)
    {
        if (!is_a($this,$property::class) || ($this::class == $property::class)) {
            throw new NotAllowedInclusionException("The inclusion 'embed' is only allowed for ancestors.");
        }
        $this->appendMemebers($property, 'embed');
    }
    
    private function checkRefer(AbstractProperty $property)
    {
        if (!is_a($property, PooledRecordProperty::class)) {
            throw new NotAllowedInclusionException("The inclusion 'embed' is only allowed for ancestors.");
        }
    }
    
    private function linkElement(AbstractProperty $property)
    {
        $property->setOwner($this);
        if (!empty($this->getStorage())) {
            $property->setStorage($this->getStorage());
        }
    }
    
    /**
     * Adds the structure of the property to the structures list
     * @param AbstractProperty $property
     */
    private function appendToElementsAndStructures(AbstractProperty $property, string $inclusion)
    {
        switch ($inclusion) {
            case 'include':
                $this->checkInclude($property);
                break;
            case 'embed':
                $this->checkEmbed($property);
                break;
            case 'refer':
                $this->checkRefer($property);
                break;
            default:
                throw new InvalidInclusionException("The inclusion '$inclusion' is not defined");
                
        }
    }
    
    public function appendElement(mixed $element, ?string $name = null, string $inclusion = 'include', $storage = null)
    {
        $element = $this->getElementProperty($element);
        if (($inclusion == 'include') && (!is_a($element, RecordProperty::class))) {
            $this->writeName($element, $name);
        }
        $this->checkForDuplicateName($element);
        $this->checkForDuplicateProperty($element);
        $this->linkElement($element);
        $this->appendToElementsAndStructures($element, $inclusion);
        return $element;
    }
    
    /**
     * Checks if a property element with the given name exists.
     * 
     * @param string $name
     * @return bool
     */
    public function hasElement(string $name): bool
    {
        return array_key_exists($name, $this->elements);
    }
    
    public function elementCount(): int
    {
        return count($this->elements);    
    }
    
    // Interface countable
    public function count(): int
    {
        return $this->elementCount();
    }
    
    private $ptr = 0;
    
    //  Interface iterator
    public function current(): mixed
    {
        return $this->elements[array_keys($this->elements)[$this->ptr]];
    }
    
    public function key(): mixed
    {
        return array_keys($this->elements)[$this->ptr];        
    }
    
    public function next(): void
    {
        $this->ptr++;        
    }
    
    public function rewind(): void
    {
        $this->ptr = 0;        
    }
    
    public function valid(): bool
    {
        return ($this->ptr >= 0) && ($this->ptr < $this->count());        
    }
    
    // Element Access
    public function __get($varname): mixed
    {
        if (!$this->hasElement($varname)) {
            throw new PropertyNotFoundException("The property '$varname' does not exist.");
        }
        $property = $this->elements[$varname];
        if (in_array($property::class,[ArrayProperty::class])) {
            return $property;
        } else {
            return $property->getValue();
        }
    }
    
    public function __set($varname, $value)
    {
        if (!$this->hasElement($varname)) {
            throw new PropertyNotFoundException("The property '$varname' does not exist.");
        }        
        $property = $this->elements[$varname];
        if (in_array($property::class,[ArrayProperty::class])) {
            return $property;
        } else {
            return $property->setValue($value);
        }
    }
    
    /**
     * Modifies the inhertied function to include the elements
     * 
     * {@inheritDoc}
     * @see \Sunhill\Properties\AbstractProperty::getStructure()
     */
    public function getStructure()
    {
        $return = parent::getStructure();
        $return->elements = [];
        foreach ($this->elements as $name => $element) {
            $return->elements[$name] = $element->getStructure();
        }
        return $return;
    }
}