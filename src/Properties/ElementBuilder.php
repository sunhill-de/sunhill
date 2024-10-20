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

use Sunhill\Properties\Exceptions\PropertyNotSetException;
use Sunhill\Properties\Exceptions\PropertyDoesntExistException;
use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\NotAPropertyException;

class ElementBuilder
{
    
    protected $owner;
    
    public function __construct(RecordProperty $owner)
    {
        $this->owner = $owner;    
    }
    
    public function addProperty(string|AbstractProperty $property_name, string $name): AbstractProperty
    {
        if (is_null($this->owner)) {
            throw new PropertyNotSetException("No property was set");
        }
        $property = $this->lookupProperty($property_name);
        $this->owner->appendElement($property_name, $name);
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
        if (is_null($namespace = Properties::getNamespaceOfClass($property_name))) {
            throw new NotAPropertyException("The given paraneter is not a valid property");
        }
        return new $namespace();
            
    }
    
    public function array(string $name): ArrayProperty
    {
        
    }
    
    public function embedRecord(string $record): RecordProperty
    {
        
    }
    
    public function includeRecord(string $record): RecordProperty
    {
        
    }
    
    public function referRecord(string $record): RecordProperty
    {
        
    }
    
}
