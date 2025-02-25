<?php
 
/**
 * @file PropertyManager.php
 * Provides the PropertyManager object for accessing information about properties.
 * Replaces the classes and collection manager
 * 
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-19-22
 * Localization: unknown
 * Documentation: all public
 * Tests: Unit/Managers/ManagerPropertiesTest.php
 * Coverage: 68.35% (2024-10-22)
 * PSR-State: complete
 */
namespace Sunhill\Managers;

use Sunhill\Managers\Exceptions\PropertyClassDoesntExistException;
use Sunhill\Managers\Exceptions\GivenClassNotAPropertyException;
use Sunhill\Managers\Exceptions\PropertyNameAlreadyRegisteredException;
use Sunhill\Managers\Exceptions\PropertyNotRegisteredException;
use Sunhill\Managers\Exceptions\UnitNameAlreadyRegisteredException;
use Sunhill\Managers\Exceptions\UnitNotRegisteredException;
use Sunhill\Objects\AbstractPersistantRecord;
use Sunhill\Objects\ObjectDescriptor;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Objects\Mysql\MysqlStorage;
use Sunhill\Properties\AbstractProperty;
use Sunhill\Query\BasicQuery;
use Sunhill\Storage\CallbackStorage;

/**
 * The PropertiesManager is accessed via the Properties facade. It's a singelton class
 */
class PropertiesManager 
{

    /**
     * Stores the currently registered properties.
     *
     * @var array
     */
    protected $registered_properties = [];
    
    public function PropertyQuery(): BasicQuery
    {
        
    }
    
    /**
     * Clears all registered properties
     */
    public function clearRegisteredProperties()
    {
        $this->registered_properties = [];
    }
    
    /**
     * After the checks this method does the registering
     *
     * @param string $property
     */
    protected function doRegisterProperty(string $property, ?string $alias)
    {
        $name = is_null($alias)?$property::getInfo('name'):$alias;
        if (isset($this->registered_properties[$name])) {
            throw new PropertyNameAlreadyRegisteredException("The name '$name' of '$property' is already regsitered.");
        }
        $this->registered_properties[$name] = $property;
    }
    
    /**
     * Registered a new property to the manager
     *
     * @param string $property
     */
    public function registerProperty(string $property, ?string $alias = null)
    {
        if (!class_exists($property)) {
            throw new PropertyClassDoesntExistException("The class '$property' is not accessible.");
        }
        if (!is_a($property, AbstractProperty::class, true)) {
            throw new GivenClassNotAPropertyException("The class '$property' is not a descendant of AbstractProperty.");
        }
        $this->doRegisterProperty($property, $alias);
    }
    
    /**
     * Internal helper that tries to translate the given $property parameter into the name (and therefore
     * key in the registered_properties array) of the property.
     *
     * @param unknown $property
     * @return string|false
     */
    protected function searchProperty($property): string|false
    {
        if (is_string($property)) {
            if (array_key_exists($property, $this->registered_properties)) {
                return $property;
            }
            if ($key = array_search($property, $this->registered_properties)) {
                return $key;
            }
        }
        if (is_object($property)) {
            return $this->searchProperty($property::class);
        }
        return false;
    }
    
    protected function searchOrThrow($property): string
    {
        if ($key = $this->searchProperty($property)) {
            return $key;
        }
        if (is_scalar($property)) {
            throw new PropertyNotRegisteredException("The property '$property' is not regsitered.");
        } else {
            throw new PropertyNotRegisteredException("The given non scalar property is not registered.");
        }
    }
    
    /**
     * Returns true, when the given name or class was already registered
     *
     * @param $property
     * @return boolean
     */
    public function isPropertyRegistered($property): bool
    {
        return ($this->searchProperty($property))==false?false:true;
    }
    
    /**
     * Returns the full classname with namespace of the given property
     *
     * @param unknown $property
     * @tests PropertiesManagerTest::testGetNamespaceOfProperty
     */
    public function getNamespaceOfProperty($property)
    {
        $key = $this->searchOrThrow($property);
        return $this->registered_properties[$key];
    }
    
    /**
     * Returns the name of the given property
     *
     * @param unknown $property
     * @return string
     * @tests PropertiesManagerTest::testGetNameOfProperty
     */
    public function getNameOfProperty($property)
    {
        $key = $this->searchOrThrow($property);
        return $key;
    }
    /**
     * Returns the structure of this property
     *
     * @param unknown $property Any kind of reference to a property.
     * @return The structure of this property
     */
    public function getStructureOfProperty($property)
    {
         $namespace = $this->getNamespaceOfrProperty($property);
         $object = new $namespace(); // getStructure() is not static !
         return $object->getStructure();
    }
 
    /**
     * Testss if the given property has a method with the given name
     *
     * @param unknown $property
     * @param unknown $method
     * @return boolean
     */
    public function propertyHasMethod($property, $method)
    {
        $namespace = $this->getNamespaceOfProperty($property);
        return method_exists($namespace, $method);
    }
    
    /**
     * Creates a property of the given type
     *
     * @param unknown $property
     * @return unknown
     */
    public function createProperty($property, ?string $name = null, mixed $storage = null)
    {
        if (!is_a($property, AbstractProperty::class)) {
            $namespace = $this->getNamespaceOfProperty($property);
            $property = new $namespace();
        }
        if (!is_null($name)) {
            $property->setName($name);
        }
        if (is_null($storage)) {
            return $property;
        }
        if (is_callable($storage)) {
            $storage_obj = new CallbackStorage();
            $storage_obj->setCallback($storage);
            $property->setStorage($storage_obj);
            return $property;
        }
        if (is_string($storage) && class_exists($storage)) {
            $storage = new $storage();
        }
        if (is_a($storage, AbstractStorage::class)) {
            $property->setStorage($storage);
            return $property;
        }
        return false;
    }
    
    /**
     * Stores the currently registered units.
     *
     * @var array
     */
    protected $registered_units = [];
    
    public function UnitQuery(): BasicQuery
    {
        
    }
    
    /**
     * Clears the currently registered units
     */
    public function clearRegisteredUnits()
    {
        $this->registered_units = [];
    }
    
    public function registerUnit(string $name, string $unit, string $group, string $basic = '',
        ?callable $calculate_to = null, ?callable $calculate_from = null)
    {
        if (isset($this->registered_units[$name])) {
            throw new UnitNameAlreadyRegisteredException("The unit '$name' is already registered.");
        }
        $this->registered_units[$name] = [
            'unit'=>$unit,
            'group'=>$group,
            'basic'=>($basic == '')?$name:$basic,
            'calculate_to'=>$calculate_to,
            'calculate_from'=>$calculate_from
        ];
    }
    
    /**
     * Returns true if the unit is registered otherwise false
     *
     * @param string $property
     * @return bool
     */
    public function isUnitRegistered(string $property): bool
    {
        return isset($this->registered_units[$property]);
    }
    
    /**
     * Checks if the given unit is registered. If not throws exception.
     * @param string $unit
     */
    protected function checkOrThrowUnit(string $unit)
    {
        if (!$this->isUnitRegistered($unit)) {
            throw new UnitNotRegisteredException("The unit '$unit' is not registered.");
        }
    }
    
    /**
     * Return the unit of the given unit name
     *
     * @param string $unit
     * @return string
     */
    public function getUnit(string $unit): string
    {
        $this->checkOrThrowUnit($unit);
        
        return $this->registered_units[$unit]['unit'];
    }
    
    /**
     * Returns the group of the given unit name
     *
     * @param string $unit
     * @return string
     */
    public function getUnitGroup(string $unit): string
    {
        $this->checkOrThrowUnit($unit);
        
        return $this->registered_units[$unit]['group'];
    }
    
    /**
     * Returns the basic unit name of the given unit name
     *
     * @param string $unit
     * @return string
     */
    public function getUnitBasic(string $unit): string
    {
        $this->checkOrThrowUnit($unit);
        
        return $this->registered_units[$unit]['basic'];
    }
    
    /**
     * Calculates the given $value to the basic unit
     *
     * @param string $unit
     * @param float $value
     * @return float
     */
    public function calculateToBasic(string $unit, float $value): float
    {
        $this->checkOrThrowUnit($unit);
        
        $calc = $this->registered_units[$unit]['calculate_to'];
        if (is_null($calc)) {
            return $value;
        }
        
        return $calc($value);
    }
    
    /**
     * Calculates the given $value from the basic unit
     *
     * @param string $unit
     * @param float $value
     * @return float
     */
    public function calculateFromBasic(string $unit, float $value): float
    {
        $this->checkOrThrowUnit($unit);
        
        $calc = $this->registered_units[$unit]['calculate_from'];
        if (is_null($calc)) {
            return $value;
        }
        return $calc($value);
    }
}
