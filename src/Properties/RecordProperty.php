<?php
/**
 * @file AbstractProperty.php
 * Defines an abstract property as base for all other properties
 * Lang de,en
 * Reviewstatus: 2024-10-07
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Properties/AbstractPropertyTest.php
 * Coverage: 98.32 (2024-10-19)
 *
 * Wiki: /Properties
 * tests /tests/Unit/Properties/AbstractProperties/*
 */

namespace Sunhill\Properties;

use Sunhill\Properties\AbstractProperty;
use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\NotAPropertyException;
use Sunhill\Properties\Exceptions\PropertyNameAlreadyGivenException;
use Sunhill\Properties\Exceptions\PropertyAlreadyInListException;
use Sunhill\Properties\Exceptions\PropertyHasNoNameException;

class RecordProperty extends AbstractProperty
{
    
    public function getAccessType(): string
    {
        return 'record';
    }

    public function isValid($input): bool
    {
        
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
    
    /**
     * Just adds the property to the elements list
     * @param AbstractProperty $property
     */
    private function appendToElements(AbstractProperty $property)
    {
        $this->elements[$property->getName()] = $property;
    }
    
    /**
     * Adds the structure of the property to the structures list
     * @param AbstractProperty $property
     */
    private function appendToStructures(AbstractProperty $property, string $inclusion)
    {
        $structure = $property->getStructure();
        if (is_a($property,RecordProperty::class)) {
            switch ($inclusion) {
                case 'include':
                    $structure->storage_subid = $this->getStorageID();
                    break;
                case 'embed':
                    break;
                case 'refer':
                    break;
                default:                    
            }
        } else {
            
        }
        $this->elements_structure[$property->getName()] = $structure;
    }
    
    public function appendElement(mixed $element, ?string $name = null, string $inclusion = 'include', $storage = null)
    {
        $element = $this->getElementProperty($element);
        $this->writeName($element, $name);        
        $this->checkForDuplicateName($element);
        $this->checkForDuplicateProperty($element);
        $this->appendToElements($element);
        $this->appendToStructures($element, $inclusion);
        return $element;
    }
}