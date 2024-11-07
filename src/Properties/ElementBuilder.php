<?php
/**
 * @file ElementBuilder.php
 * A mediator class for adding elements to a record property
 * Lang en
 * Reviewstatus: 2024-09-29
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/PropertyTest.php, Unit/PropertyValidateTest.php
 * Coverage: 68.42% (2024-10-17)
 */

namespace Sunhill\Properties;

use Sunhill\Properties\Exceptions\PropertyDoesntExistException;
use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\NotAPropertyException;

class ElementBuilder
{
    
    /**
     * Stores the added elements
     * 
     * @var array
     */
    protected $elements = [];
    
    /**
     * Returns the added elements
     * 
     * @return array
     */
    public function getElements(): array
    {
        return $this->elements;    
    }
    
    /**
     * Stores included record properties
     * 
     * @var array
     */
    protected $includes = [];
    
    /**
     * Returens the included record properties
     * 
     * @return array
     */
    public function getIncludes(): array
    {
        return $this->includes;
    }
    
    public function addProperty(string|AbstractProperty $property_name, string $name): AbstractProperty
    {
        $property = $this->lookupProperty($property_name);
        $this->elements[$name] = $property;
        return $property;
    }
    
    public function lookUpProperty(string|AbstractProperty $property_name): AbstractProperty
    {
        if (is_a($property_name,AbstractProperty::class)) {
            return $property_name;
        }
        if (class_exists($property_name)) {
            return new $property_name();
        }
        if (!is_string($property_name)) {
            throw new PropertyDoesntExistException("The given property does not exist.");
        }
        if (is_null($namespace = Properties::getNamespaceOfProperty($property_name))) {
            throw new NotAPropertyException("The given paraneter is not a valid property");
        }
        return new $namespace();
            
    }
    
    public function array(string|ArrayProperty $property, string $name): ArrayProperty
    {
        
    }
    
    public function arrayOfReferences(string|PooledRecordProperty $property, string $name): ArrayProperty
    {
        
    }
    
    public function includeRecord(string|RecordProperty $record, string $name): RecordProperty
    {
        
    }
    
    public function referRecord(string|PooledRecordProperty $record, string $name): ReferenceProperty
    {
        
    }
    
    public function __call(string $method,array $params)
    {
        
    }
}
