<?php

namespace Sunhill\Properties\Objects;

use Sunhill\Properties\Properties\AbstractRecordProperty;
use Sunhill\Properties\Properties\AbstractProperty;
use Sunhill\Properties\Facades\Properties;
use Sunhill\Properties\Properties\AbstractArrayProperty;
use Sunhill\Properties\Objects\Exceptions\TypeCannotBeEmbeddedException;
use Sunhill\Properties\Objects\Exceptions\TypeAlreadyEmbeddedException;
use Sunhill\Properties\Properties\Exceptions\DuplicateElementNameException;

class AbstractPersistantRecord extends AbstractRecordProperty
{
    
    protected $elements = [];
    
    protected $storage_ids = [];
    
    protected $includes = [];
    
    protected $embeds = [];
    
    public function __construct()
    {
        $this->collectProperties();
    }
    
    protected static function initializeProperties(ObjectDescriptor $descriptor)
    {
    }

    /**
     * Returns if the current record has an own initalizeProperties() method
     * 
     * @param string $source
     * @return bool
     */
    protected function hasOwnProperties(string $source): bool
    {
        $reflector = new \ReflectionMethod($source, 'initializeProperties');
        return ($reflector->getDeclaringClass()->getName() == $source); 
    }

    /**
     * Returns the name of the storage that belongs to the according record class
     * 
     * @param string $class
     * @return string
     */
    protected function getSourceStorage(string $class): string
    {
        if (static::handleInheritance() == 'include') {
            return Properties::getStorageIDOfRecord($this::class);   
        } else {
            return Properties::getStorageIDOfRecord($class);
        }
    }
    
    /**
     * Collects all properties from the given class and adds it to the current record. Sets the storage_id depending
     * on the result of getSourceStorage()
     * 
     * @param string $class
     * @param ObjectDescriptor $descriptor
     */
    protected function collectPropertiesFrom(string $class, ObjectDescriptor $descriptor)
    {
        $descriptor->setSourceStorage($this->getSourceStorage($class));
        if ($this->hasOwnProperties($class)) {
            $class::initializeProperties($descriptor);
        }
    }
    
    /**
     * This method is called by the construtor and calls for every member of the ancestor list the method
     * initializeProperties
     */
    protected function collectProperties()
    {
        $descriptor = Properties::getObjectDescriptorForRecord($this);
        $hirachy = Properties::getHirachyOfRecord($this::class);
        foreach ($hirachy as $ancestor) {
            $this->collectPropertiesFrom($ancestor, $descriptor);
        }
    }
    
    public function getElementNames()
    {
        return array_keys($this->elements);    
    }
    
    public function getElements()
    {
        return $this->elements;
    }
    
    public function getElementValues()
    {
        return array_values($this->elements);
    }
    
    public function hasElement(string $name): bool
    {
        return isset($this->elements[$name]);
    }
    
    public function getElement(string $name): AbstractProperty
    {
        return $this->elements[$name];
    }
    
    protected function solveProperty(string $name): AbstractProperty
    {
        $return = Properties::createProperty($name);
        return $return;
    }
    
    protected function getRecordProperty(string $classname): AbstractProperty
    {
        $property = $this->solveProperty($classname);
        if (!is_a($property, AbstractRecordProperty::class)) {
            throw new TypeCannotBeEmbeddedException("The given type '$classname' cant be embedded/included");
        }
        return $property;
    }
    
    /**
     * Is called when an element name is already given. Could be later used for a more intelligent way to
     * handle name collisions.
     * 
     * @param string $name
     */
    protected function handleDuplicateElements(string $name)
    {
        throw new DuplicateElementNameException("The element '$name' is already given");        
    }
    
    /**
     * Checks if the name is already given and inserts the element into the element list
     * 
     * @param string $name
     * @param AbstractProperty $element
     */
    protected function handleElement(string $name, AbstractProperty $element)
    {
        if (isset($this->elements[$name])) {
            $this->handleDuplicateElements($name);
        }
        $this->elements[$name] = $element;        
    }
    
    /**
     * Inserts the element in the storage association list.
     * 
     * @param string $name
     * @param AbstractProperty $element
     * @param string $storage_id
     */
    protected function handleStorageID(string $name, AbstractProperty $element, string $storage_id)
    {
        if (isset($this->storage_ids[$storage_id])) {
            $this->storage_ids[$storage_id][$name] = $element;
        } else {
            $this->storage_ids[$storage_id] = [$name => $element];
        }        
    }
    
    protected function insertElement(string $name, AbstractProperty $element, string $storage_id)
    {
        $this->handleElement($name, $element);
        $this->handleStorageID($name, $element, $storage_id);
    }
    
    protected function insertElements(AbstractRecordProperty $property, string $kind)
    {
        foreach ($property->getElements() as $name => $element) {
            $this->insertElement($name, $element, ($kind == 'embed')?$this->getStorageID():static::getInfo('storage_id'));
        }
    }

    protected function getStorageID()
    {
        return static::getInfo('storage_id');    
    }
    
    protected function embedOrIncludeElement(string $classname, string $kind): AbstractProperty
    {
        if ($this->hasEmbed($classname)) {
            throw new TypeAlreadyEmbeddedException("The class '$classname' is already embedded");
        }
        if ($this->hasInclude($classname)) {
            throw new TypeAlreadyEmbeddedException("The class '$classname' is already included");
        }
        
        $property = $this->getRecordProperty($classname);
        $this->insertElements($property, 'embed');
        
        return $property;
    }
    
    public function embedElement(string $classname): AbstractProperty
    {
        $property = $this->embedOrIncludeElement($classname, 'embed');
        $this->embeds[$classname] = $property;
                
        return $property;
    }
    
    public function includeElement(string $classname): AbstractProperty
    {
        $property = $this->embedOrIncludeElement($classname, 'include');
        $this->includes[$classname] = $property;
                
        return $property;
    }
        
    public function appendElement(string $element_name, string $class_name): AbstractProperty
    {
        $property = $this->solveProperty($class_name);
        switch ($property::class) {
            case AbstractArrayProperty::class:
                $this->insertElement($element_name, $property, static::class,'array');
                break;
            case AbstractRecordProperty::class:
                $this->insertElement($element_name, $property, static::class, 'record');
                break;
            default:   
                $this->insertElement($element_name, $property, static::class,'simple');
                break;
        }
        return $property;
    }
    
    public function hasInclude(string $classname): bool
    {
        return isset($this->includes[$classname]);
    }
    
    public function hasEmbed(string $classname): bool
    {
        return isset($this->embeds[$classname]);        
    }

    public function exportElements(): array
    {
        $result = [];
        return $result;
    }
    
    public function isDirty(): bool
    {
        return $this->getStorage()->isDirty();
    }
    
    /**
     * Passes the request to the according storage and tells it to commit any changes made to it.
     * 
     * {@inheritDoc}
     * @see Sunhill\\Properties\AbstractProperty::commit()
     */
    public function commit()
    {
        return $this->getStorage()->commit();        
    }
    
    /**
     * Passes the request to the according stoarga and tells it to rollback any changes that was made to it since
     * the last commit.
     * 
     * {@inheritDoc}
     * @see Sunhill\\Properties\AbstractProperty::rollback()
     */
    public function rollback()
    {
        return $this->getStorage()->rollback();        
    }
    
    /**
     * Does all necessary steps to initialize a storage for the persistant record. That could be for exampke create 
     * database tables, etc. The request is passed to the according storage
     * 
     * @return unknown
     */
    public function migrate()
    {
        return $this->getStorage()->migrate();
    }
    
    /**
     * Upgrades the persistant record to a higher record of the same hirachy path. The request is passed to the
     * according storage
     * 
     * @param string $target_class
     * @return unknown
     */
    public function upgrade(string $target_class)
    {
        return $this->getStorage()->upgrade($target_class);        
    }
    
    /**
     * Degrades the persistant record to a lower record of the same hirachy path. The request is passed to the
     * according storage
     * 
     * @param string $target_class
     * @return unknown
     */
    public function degrade(string $target_class)
    {
        return $this->getStorage()->degrade($target_class);        
    }
    
    /**
     * Passes a query request to the according storage
     * 
     * @return unknown
     */
    public function query()
    {
        return $this->getStorage()->query();        
    }
    
}

