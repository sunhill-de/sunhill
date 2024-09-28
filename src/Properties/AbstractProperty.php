<?php
/**
 * @file AbstractProperty.php
 * Defines an abstract property as base for all other properties
 * Lang de,en
 * Reviewstatus: 2024-02-04
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Properties/AbstractPropertyTest.php
 * Coverage: 87.82 (2024-03-08)
 * 
 * Wiki: /Properties
 */

namespace Sunhill\Properties;

use Sunhill\Properties\Exceptions\InvalidNameException;
use Sunhill\Properties\Exceptions\UninitializedValueException;
use Sunhill\Properties\Exceptions\PropertyNotReadableException;
use Sunhill\Properties\Exceptions\UserNotAuthorizedForReadingException;
use Sunhill\Properties\Exceptions\NoUserManagerInstalledException;
use Sunhill\Properties\Exceptions\PropertyNotWriteableException;
use Sunhill\Properties\Exceptions\UserNotAuthorizedForWritingException;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Properties\Exceptions\NoStorageSetException;
use Sunhill\Properties\Exceptions\PropertyException;
use Sunhill\Properties\Exceptions\InvalidValueException;
use Illuminate\Support\Facades\Log;
use Sunhill\Properties\Exceptions\UserNotAuthorizedForModifyException;
use Sunhill\Properties\Exceptions\InvalidTypeOrSemanticException;
use Sunhill\Properties\Exceptions\PropertyKeyDoesntExistException;
use Sunhill\Facades\Properties;
use Sunhill\Query\Exceptions\NotAllowedRelationException;
use Sunhill\Query\Exceptions\WrongTypeException;

abstract class AbstractProperty
{

    /**
     * Stores the current owner of this property (if any)
     * 
     * @var unknown
     */
    protected $owner;
    
    /**
     * Sets the current owner of this property
     * 
     * @param AbstractProperty $owner
     * @return AbstractProperty
     * 
     * @wiki Properties#Ownership of propeties
     */
    public function setOwner(AbstractProperty $owner): AbstractProperty
    {
        $this->owner = $owner;
        
        return $this;
    }
    
    /**
     * Returns the current owner of this property or null
     * 
     * @return AbstractProperty|NULL
     * 
     * @wiki Properties#Ownership of propeties
     */
    public function getOwner(): ?AbstractProperty
    {
        return $this->owner;
    }
    
    /**
     * Return the path of this element (the names of all ancestors until this element)
     * 
     * @return string
     * 
     * @wiki Properties#Ownership of propeties
     */
    public function getPath(): string
    {
        if (is_null($this->getOwner())) {
            return $this->getName()??'undefined';
        } else {
            return $this->getOwner()->getPath().'.'.($this->getName()??'undefined');
        }
    }
    
    /**
     * Stores the current storage
     * 
     * @var unknown
     */
    protected $storage;
    
    /**
     * Setter for $storage
     * 
     * @param AbstractStorage $storage
     * @return Sunhill\\Properties\AbstractProperty
     * 
     * @wiki /Properties#setStorage()_and_getStorage()
     * 
     * @test AbstractPropertyTest::testSetStorage()
     */
    public function setStorage(AbstractStorage $storage)
    {
        $this->storage = $storage;
        return $this;
    }
    
    /**
     * Getter for storage
     * 
     * @return AbstractStorage
     * 
     * @wiki /Properties#setStorage()_and_getStorage()
     * 
     * @test AbstractPropertyTest::testSetStorage()
     */
    public function getStorage(): AbstractStorage
    {
        return $this->storage;
    }
    
    /**
     * Checks if a storage is set. If not raises an exception
     * 
     * @param string $action
     * @throes NoStorageExcveption
     * 
     * @test AbstractPropertyTest::testNoStorage()
     */
    protected function checkForStorage(string $action)
    {
        if (empty($this->storage)) {
            throw new NoStorageSetException("There is no storage set: $action");
        }
    }
    
// ====================================== Name =====================================================    
    /**
     * The name of this property
     * Property->getName() reads, Property->setName() writes
     * @var string
     */
    protected $_name = "";
    
    /**
     * A class constant for defining forbidden names for properties
     * @var array
     */
    const FORBIDDEN_NAMES = ['object','string','integer','float','boolean','collection', 'id', 'classname'];

    /**
     * Checks the designated name for this property if it is valid. If not is raises an exception
     * 
     * @param string $name
     * @exception InvalidNameException When the given name is not valid
     * 
     * @test AbstractPropertyTest::testNames()
     */
    protected function checkName(string $name)
    {
        if (empty($name)) {
            throw new InvalidNameException("The property name '$name' must not be empty.");
        }
        if ($name[0] == '_') {
            throw new InvalidNameException("The property name '$name' must not start with an underscore.");
        }
        if (in_array(strtolower($name), static::FORBIDDEN_NAMES)) {
            throw new InvalidNameException("The property name '$name' is reserved and not allowed.");
        }
    }
    
    /**
     * sets the field Property->name
     * @param $name The name of the property
     * @return PropertyOld a reference to this to make setter chains possible
     *
     * Test Unit/Properties/PropertyTest::testNames()
     *
     * @wiki Properties#Name of property
     */
    public function setName(string $name): AbstractProperty
    {
        $this->checkName($name);
        $this->_name = $name;
        return $this;
    }
    
    /**
     * Skips the name checking (for system properties)
     * @param string $name
     * @return Proeprty
     *
     * Test Unit/Properties/PropertyTest::forceNames()
     *
     * @wiki Properties#Name of property
     */
    public function forceName(string $name): AbstractProperty
    {
        $this->_name = $name;
        return $this;
    }
    
    /**
     * Alias for setName()
     *
     * @param string $name
     * @return PropertyOld
     *
     * Test Unit/Properties/PropertyTest::testNames
     *
     * @wiki Properties#Name of property
     */
    public function name(string $name): AbstractProperty
    {
        return $this->setName($name);
    }
    
    /**
     * Returns the name of this property
     *
     * Test Unit/Properties/PropertyTest::testNames
     *
     * @wiki Properties#Name of property
     */
    public function getName(): ?string
    {
        return $this->_name;
    }
    
    /**
     * Returns true, when the passed name is a valid property name otherwise false
     * 
     * @param string $test
     * @return bool
     *
     * Test Unit/Properties/PropertyTest::testNames
     *
     * @wiki Properties#Name of property
     */
    public function isValidPropertyName(string $test): bool
    {
        try {
            $this->checkName($test);
        } catch (InvalidNameException $e) {
            return false;
        }
        return true;
    }

// ==================================== Value handling ======================================    
    /**
     * A static variable that stores the current user manager (a facade or static interface
     * that implements a "hasCapability()" method
     * 
     * @var string
     */
    protected static $current_usermanager_fascade = '';
    
    /**
     * Initializes a user mangement interface for all properties. If any other user mangement than
     * the one from the sunhill framework is installed, here has to be an interface than defines the
     * hasCapability() method. This should return true when the current user has the given capability 
     * otherwise false. 
     * 
     * @param string $user_manager
     * 
     * @test AbstractPropertyTest::testGetCapabilities
     * 
     * @wiki /Properties#Capabilities
     */
    public static function setUserManager(string $user_manager)
    {
        self::$current_usermanager_fascade = $user_manager;    
    }
    
    protected string $read_capability = '';
    
    /**
     * Returns the required capability to read this property or null if none is required
     * 
     * @return string|NULL
     * 
     * @test AbstractPropertyTest::testGetCapabilities
     * 
     * @wiki /Properties#Capabilities
     */
    public function readCapability(): ?string
    {
        return $this->read_capability;
    }
    
    /**
     * Alias for readCapability
     * 
     * @return string|NULL
     * 
     * @wiki /Properties#Capabilities
     */
    public function getReadCapability(): ?string
    {
        return $this->readCapability();
    }
    
    /**
     * Sets the read capabilities for this property
     *
     * @return AbstractProperty
     *
     * @wiki /Properties#Capabilities
     */
    public function setReadCapability(string $capability): AbstractProperty
    {
        $this->read_capability = $capability;
        return $this;
    }
    
    protected bool $is_readable = true;
    
    /**
     * Returns true, when the property is readable
     * 
     * @return bool true, if the property is readable otherwise false
     * 
     * @test AbstractPropertyTest::testPropertyNotReadable
     * 
     * @wiki /Properties#Capabilities
     */
    public function isReadable(): bool
    {
        return $this->is_readable;
    }
    
    /**
     * Alias to isReadable()
     * 
     * @return bool
     * 
     * @wiki /Properties#Capabilities
     */
    public function getReadable(): bool
    {
        return $this->isReadable();
    }
    
    /**
     * Marks the property as readable or not readable
     * 
     * @param bool $readable
     * @return AbstractProperty
     * 
     * @wiki /Properties#Capabilities
     */
    public function setReadable(bool $readable = true): AbstractProperty
    {
        $this->is_readable = $readable;
        return $this;
    }
    
    /**
     * Checks if this property is readable. If not it raises an exception
     * 
     * @throws PropertyNotReadableException::class When this property is not readbale
     * 
     * @test AbstractPropertyTest::testPropertyNotReadable()
     */
    private function checkIsReadable()
    {
        if (!$this->isReadable()) {
            throw new PropertyNotReadableException("The property '".$this->_name."' is not readable.");
        }
    }

    /**
     * Checks if a user manager is installed. If yes it checks if the current user has the capability
     * to read this property
     * 
     * @param string $capability
     * @throws NoUserManagerInstalledException::class When no user manager is installed
     * @throws UserNotAuthorizedForReadingException::class When the current user is not authorized to read
     * 
     *  @test AbstractPropertyTest::testNoUserManagerInstalled()
     *  @test AbstractPropertyTest::testUserNotAuthorizedForReading()
     *  @test AbstractPropertyTest::testUserAuthorizedForReading()
     */
    private function doCheckReadCapability(string $capability)
    {
        if (empty(static::$current_usermanager_fascade)) {
            throw new NoUserManagerInstalledException("Property has a read restriction but no user manager is installed.");
        }
        if (!static::$current_usermanager_fascade::hasCapability($capability)) {
            throw new UserNotAuthorizedForReadingException("The current user is not authorized to read '".$this->_name."'");
        }
    }
    
    /**
     * Checks if this property has any restrictions for reading at all and if yes if the 
     * current user has this capability.
     * 
     * 
     *  @test AbstractPropertyTest::testNoUserManagerInstalled()
     *  @test AbstractPropertyTest::testUserNotAuthorizedForReading()
     *  @test AbstractPropertyTest::testUserAuthorizedForReading()
     */
    private function checkIsAuthorizedForReading()
    {
       $capability = $this->readCapability();
       
       if (empty($capability)) {
           return; // If capability is empty, just leave
       }
       
       $this->doCheckReadCapability($capability);
    }
    
    /**
     * Call this method before any reading attempts
     * 
     *  @test AbstractPropertyTest::testPropertyNotReadable()
     *  @test AbstractPropertyTest::testNoUserManagerInstalled()
     *  @test AbstractPropertyTest::testUserNotAuthorizedForReading()
     *  @test AbstractPropertyTest::testUserAuthorizedForReading()
     */
    protected function checkForReading()
    {
        $this->checkIsReadable();
        $this->checkIsAuthorizedForReading();        
    }
    
    /**
     * Performs the reading process
     * 
     *  @test AbstractPropertyTest::testPropertyNotReadable()
     *  @test AbstractPropertyTest::testNoUserManagerInstalled()
     *  @test AbstractPropertyTest::testUserNotAuthorizedForReading()
     *  @test AbstractPropertyTest::testUserAuthorizedForReading()
     */
    protected function doGetValue()
    {
        if ($this->getStorage()->getIsInitialized($this->getName())) {
            return $this->formatFromStorage($this->getStorage()->getValue($this->getName()));
        } else {
            return $this->handleUninitialized();
        }
    }
    
    protected function handleUninitialized()
    {
        if ($this->hasDefault()) {
            return $this->getDefault();
        }
        throw new UninitializedValueException("Reading access to uninitialized property: ".$this->getName());    
    }
    
    /**
     * Checks the reading restrictions and if passed performs the reading
     * 
     * @return unknown
     * 
     *  @test AbstractPropertyTest::testPropertyNotReadable()
     *  @test AbstractPropertyTest::testNoUserManagerInstalled()
     *  @test AbstractPropertyTest::testUserNotAuthorizedForReading()
     *  @test AbstractPropertyTest::testUserAuthorizedForReading()
     *  
     * @wiki /Properties#Reading_a_property_value
     */
    public function getValue()
    {
        $this->checkForReading();
        $this->checkForStorage('read');
        return $this->doGetValue();
    }

    /**
     * Returns the value in a human readable format. The possible read restrictions are already
     * checked
     * @param unknown $input
     * @return unknown
     * 
     * @wiki /Writing_own_property_classes#Human_readable_format_/_storage_format
     */
    protected function formatForHuman($input)
    {
        if (($unit = $this->getUnit()) == 'none') {
            return $input;
        } else {
            $unit = Properties::getUnit($this->getUnit());
            return $input.' '.$unit;
        }
    }
    
    /**
     * Returns the value for saving in the storafge
     * 
     * @param unknown $input
     * @return unknown
     * 
     * @wiki /Writing_own_property_classes#Human_readable_format_/_storage_format
     */
    protected function formatForStorage($input)
    {
        return $input;
    }
    
    /**
     * Returns the value as loaded from a storage
     * 
     * @param unknown $input
     * @return unknown
     * 
     * @wiki /Writing_own_property_classes#Human_readable_format_/_storage_format
     */
    protected function formatFromStorage($input)
    {
        return $input;
    }
    
    /**
     * Returns the value in a human readable form 
     * 
     * @return Sunhill\\Properties\unknown
     * 
     * @tests AbstractPropertyTest::testFormatForHuman
     * 
     * @wiki /Properties#Readubg_a_property_value
     */
    public function getHumanValue()
    {
        $this->checkForStorage('read');
        $this->checkForReading();
        return $this->formatForHuman($this->doGetValue());
    }

    protected $default;
    
    /**
     * Sets a default value for this storage
     * 
     * @param mixed $default
     * @return static
     * 
     * @wiki /Properties#Default_value
     */
    public function setDefault(mixed $default): Self
    {
        if (is_null($default)) {
            $this->default = new DefaultNull();
            $this->nullable = true;
        } else {
            $this->default = $default;
        }
        return $this;
    }

    /**
     * Sets a default value for this storage (alias for setDefault()) 
     *
     * @param mixed $default
     * @return static
     *
     * @wiki /Properties#Default_value
     */    
    public function default(mixed $default): self
    {
        return $this->setDefault($default);    
    }
    
    /**
     * Returns the current set default value
     * 
     * @return mixed
     *
     * @wiki /Properties#Default_value
     */
    public function getDefault(): mixed
    {
        if (is_a($this->default,DefaultNull::class)) {
            return null;
        }
        return $this->default;
    }

    /**
     * Returns if the default value for this property is null
     * 
     * @return bool
     *
     * @wiki /Properties#Default_value
     */
    public function defaultsNull(): bool
    {
        return is_a($this->default,DefaultNull::class);
    }

    /**
     * Returns if this property has a default value
     * 
     * @return bool
     *
     * @wiki /Properties#Default_value
     */
    public function hasDefault(): bool
    {
        return !is_null($this->default);
    }
    
    protected string $write_capability = '';
    
    /**
     * Returns the required capability to write this property or null if none is required
     *
     * @return string|NULL
     *
     * @test AbstractPropertyTest::testGetCapabilities
     *
     * @wiki /Properties#Capabilities
     */
    public function writeCapability(): ?string
    {
        return $this->write_capability;
    }
    
    /**
     * Alias for writeCapability
     *
     * @return string|NULL
     *
     * @wiki /Properties#Capabilities
     */
    public function getWriteCapability(): ?string
    {
        return $this->writeCapability();
    }
    
    /**
     * Sets the write capabilities for this property
     *
     * @return AbstractProperty
     *
     * @wiki /Properties#Capabilities
     */
    public function setWriteCapability(string $capability): AbstractProperty
    {
        $this->write_capability = $capability;
        return $this;
    }
    
    protected bool $is_writeable = true;
    
    /**
     * Returns true, when the property is writeable
     *
     * @return bool true, if the property is writeable otherwise false
     *
     * @test AbstractPropertyTest::testPropertyNotReadable
     *
     * @wiki /Properties#Capabilities
     */
    public function isWriteable(): bool
    {
        return $this->is_writeable;
    }
    
    /**
     * Alias to isWriteable()
     *
     * @return bool
     *
     * @wiki /Properties#Capabilities
     */
    public function getWriteable(): bool
    {
        return $this->isWriteable();
    }
    
    /**
     * Marks the property as readable or not readable
     *
     * @param bool $readable
     * @return AbstractProperty
     *
     * @wiki /Properties#Capabilities
     */
    public function setWriteable(bool $writeable = true): AbstractProperty
    {
        $this->is_writeable = $writeable;
        return $this;
    }
        
    /**
     * Returns true, when this property was already modified by an user. This is important for
     * a eventually existing modifyCapability
     * 
     * @return bool
     */
    public function isInitialized(): bool
    {
        $this->checkForStorage('isInitialized');
        return $this->getStorage()->getIsInitialized($this->getName());
    }
    
    protected $modify_capability = '';
    
    /**
     * Returns the required capability to modify this property or null if none is required
     *
     * @return string|NULL
     * 
     * @test AbstractPropertyTest::testGetCapabilities
     * 
     * @wiki /Properties#Capabilities
     */
    public function modifyCapability(): ?string
    {
        return $this->modify_capability;
    }
    
    /**
     * Alias for modifyCapability()
     * 
     * @return string|NULL
     * 
     * @wiki /Properties#Capabilities
     */
    public function getModifyCapability(): ?string
    {
        return $this->modifyCapability();    
    }
    
    /**
     * Setter for modify_capability
     * 
     * @param string $capability
     * @return static
     * 
     * @wiki /Properties#Capabilities
     */
    public function setModifyCapability(string $capability): self
    {
        $this->modify_capability = $capability;
        return $this;
    }
    
    /**
     * Checks if this property is writeable. If not it raises an exception
     *
     * @throws PropertyNotWriteableException::class When this property is not writeable
     * @test AbstractPropertyTest::testPropertyNotWriteable
     */
    private function checkIsWriteable()
    {
        if (!$this->isWriteable()) {
            throw new PropertyNotWriteableException("The property '".$this->_name."' is not writeable.");
        }
    }
    
    /**
     * Checks if a user manager is installed. If yes it checks if the current user has the capability
     * to write this property
     *
     * @param string $capability
     * @throws NoUserManagerInstalledException::class When no user manager is installed
     * @throws UserNotAuthorizedForWritingException::class When the current user is not authorized to write
     * @test AbstractPropertyTest::testNoUserManagerInstalledWhileWriting()
     * 
     */
    private function doCheckWriteCapability(string $capability)
    {
        if (empty(static::$current_usermanager_fascade)) {
            throw new NoUserManagerInstalledException("Property has a read restriction but no user manager is installed.");
        }
        if (!static::$current_usermanager_fascade::hasCapability($capability)) {
            throw new UserNotAuthorizedForWritingException("The current user is not authorized to write '".$this->_name."'");
        }
    }
    
    /**
     * Checks if this property has any restrictions for writing at all and if yes if the
     * current user has this capability.
     *
     * @test AbstractPropertyTest::testUserNotAuthorizedForWriting()
     * @test AbstractPropertyTest::testUserAuthorizedForWriting()
     */
    private function checkIsAuthorizedForWriting()
    {
        $capability = $this->writeCapability();
        
        if (empty($capability)) {
            return; // If capability is empty, just leave
        }
        
        $this->doCheckWriteCapability($capability);
    }
    
    /**
     * Call this method before any writing attempts
     * 
     * @test AbstractPropertyTest::testPropertyNotWriteable
     * @test AbstractPropertyTest::testUserNotAuthorizedForWriting()
     * @test AbstractPropertyTest::testUserAuthorizedForWriting()
     */
    protected function checkForWriting()
    {
        $this->checkIsWriteable();
        $this->checkIsAuthorizedForWriting();
    }

    /**
     * Checks if a user manager is installed. If yes it checks if the current user has the capability
     * to write this property
     *
     * @param string $capability
     * @throws NoUserManagerInstalledException::class When no user manager is installed
     * @throws UserNotAuthorizedForWritingException::class When the current user is not authorized to write
     * @test AbstractPropertyTest::testUserAuthorizedForModify
     * @test AbstractPropertyTest::testUserNotAuthorizedForModify
     */
    private function doCheckModifyCapability(string $capability)
    {
        if (empty(static::$current_usermanager_fascade)) {
            throw new NoUserManagerInstalledException("Property has a read restriction but no user manager is installed.");
        }
        if (!static::$current_usermanager_fascade::hasCapability($capability)) {
            throw new UserNotAuthorizedForModifyException("The current user is not authorized to modify '".$this->_name."'");
        }
    }
    
    /**
     * Checks if this property has any restrictions for modifying at all and if yes if the
     * current user has this capability.
     *
     * @test AbstractPropertyTest::testUserAuthorizedForModify
     * @test AbstractPropertyTest::testUserNotAuthorizedForModify
     */
    private function checkIsAuthorizedForModify()
    {
        $capability = $this->modifyCapability();
        
        if (empty($capability)) {
            return; // If capability is empty, just leave
        }
        
        $this->doCheckModifyCapability($capability);
    }
    
    /**
     * Call this method before any modify attempts
     * @test AbstractPropertyTest::testUserAuthorizedForModify
     * @test AbstractPropertyTest::testUserNotAuthorizedForModify
     * @test AbstractPropertyTest::testPropertyNoWriteableWhileModify
     */
    protected function checkForModify()
    {
        $this->checkIsWriteable();
        $this->checkIsAuthorizedForModify();
    }
    
    /**
     * Sometimes a property accepts several different input types. With this method those inputs
     * can be normalized
     * @param unknown $input
     * @return unknown
     * 
     * @wiki /Writing_own_property_classes#formatFromInput
     */
    protected function formatFromInput($input)
    {
        return $input;    
    }
    
    /**
     * Performs the writing process
     *
     * @test AbstractPropertyTest::testDoSetValue  
     */
    protected function doSetValue($value)
    {
        if (is_null($value)) {
            $this->getStorage()->setValue($this->getName(), null);            
        } else {
            $this->getStorage()->setValue($this->getName(), $this->formatForStorage($this->formatFromInput($value)));
        }
    }
    
    /**
     * Returns true, if the given value is accepted as an input value for this validator
     *
     * @param unknown $input The value to test
     * @return bool true if valid otherwise false
     * @test AbstractPropertyTest::testValidateInput
     * 
     *  @wiki /Properties#Writing_a_proeprty_value
     *  @wiki /Writing_own_properties
     */
    abstract public function isValid($input): bool;
    
    /**
     * Checks if the given input value is acceptes, If not it raises an exception
     *
     * @param unknown $input
     * @throws InvalidValudException is thrown when the given valu is not valid
     * @test AbstractPropertyTest::testValidateInput  
     */
    protected function validateInput($input)
    {
        if (!$this->isValid($input)) {
            if (is_scalar($input)) {
                throw new InvalidValueException("The value '$input' is not valid.");
            } else {
                throw new InvalidValueException("The value is not valuid.");
            }
        }
    }
   
    protected $nullable = false;
    
    /**
     * Marks this property as nullable (null may be assigned as value). If there is
     * not already a default value, set null as default too
     *
     * @param bool $value
     * @return PropertyOld
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function nullable(bool $value = true): self
    {
        $this->nullable = $value;
        if (!is_a($this->default,DefaultNull::class)) {
            $this->default = new DefaultNull();
        }
        return $this;
    }
    
    /**
     * Alias for nullable()
     *
     * @param bool $value
     * @return PropertyOld
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function setNullable(bool $value = true): self
    {
        return $this->nullable($value);
    }
    
    /**
     * Alias for nullable(false)
     *
     * @return PropertyOld
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function notNullable(): self
    {
        return $this->nullable(false);
    }
    
    /**
     * Getter for nullable
     *
     * @return bool
     *
     * Test: Unit/Properties/PropertyTest::testDefault
     */
    public function getNullable(): bool
    {
        return $this->nullable;
    }
        
    /**
     * Checks the writing restrictions and if passed performs the writing
     *
     * @return unknown
     * 
     *  @wiki /Properties#Writing_a_proeprty_value
     */
    public function setValue($value)
    {
        if ($this->isInitialized()) {
            $this->checkForModify();
        } else {
            $this->checkForWriting();
        }
        if (is_null($value)) {
            $this->handleNullValue();
        } else {
            $this->validateInput($value);
        }
        $this->checkForStorage('write');
        return $this->doSetValue($value);
    }
 
    /**
     * Is called whenever null is assigned as a value for this property.
     * 
     * @wiki /Writing_own_property_classes#handleNullValue()
     */
    protected function handleNullValue()
    {
        if (!$this->nullable) {
            throw new InvalidValueException("Null is not allowed as a value");            
        }
    }
    
    /**
     * Is called to make a change to this property persistant
     * 
     * @wiki /Properties#commit()_and_rollback()
     */
    public function commit()
    {
        $this->checkForStorage('commit');
        $this->getStorage()->commit();
    }
    
    /**
     * Is called to undo a change to this property
     *
     * @wiki /Properties#commit()_and_rollback()
     */
    public function rollback()
    {
        $this->checkForStorage('rollback');    
        $this->getStorage()->rollback();
    }
    
// *************************************** Metadata **********************************************

    /**
     * Returns the unique id string for the semantic of this property
     * 
     * @return string
     * 
     * @wiki /Properties#Metadata
     */
    public function getSemantic(): string
    {
        return 'none';
    }
    
    /**
     * Returns some keywords to the current semantic
     *
     * @return array
     * 
     * @wiki /Properties#Metadata
     */
    public function getSemanticKeywords(): array
    {
        return [];
    }
    
    /**
     * Returns the unique id string for the unit of this property
     * 
     * @return string
     * 
     * @wiki /Properties#Metadata
     */
    public function getUnit(): string
    {
        return 'none';
    }
    
    /**
     * Returns ths suggested update frequency
     * 
     * The ressults mean:
     * - ASAP = No caching suggested, always request value directly
     * - second = A short time caching for a couple of seconds is possible
     * - minute = Cache for about one minutze
     * - hour = Cache for about one hour
     * - late = The value doesn't change often, update only from time to time
     * @return string
     * 
     * @wiki /Properties#Metadata
     */
    public function getUpdate(): string
    {
        return 'ASAP';    
    }
    
    /**
     * Returns the access type of this property. The access type is the hint in the metadata how
     * this property could be processed. The access type is not equivalent to the type of the property
     * 
     * Access type could be:
     * - string
     * - ingteger
     * - date
     * - datetime
     * - time
     * - float
     * - boolean
     * - array
     * - record
     * 
     * @return string
     * 
     * @wiki /Properties#Metadata
     */
    abstract public function getAccessType(): string;
    
    /**
     * Assembles the metadata of this property and returns them as a associative array
     * 
     * @return string[]
     * 
     * @wiki /Properties#Metadata
     */
    public function getMetadata()
    {
        $result = [];
        $result['semantic'] = $this->getSemantic();
        $result['semantic_keywords'] = $this->getSemanticKeywords();
        $result['unit'] = $this->getUnit();
        $result['type'] = $this->getAccessType();
        $result['update'] = $this->getUpdate();
        return $result;
    }
    
// ========================= Infomarket functionallity =========================================
    /**
     * Some atomar properties could have pseudo child elements (like count for arrays)
     * @param string $name
     * @return NULL
     * 
     * @wiki /Writing_own_property_classes#InfoMarket_interaction
     */
    protected function requestTerminalItem(string $name)
    {
        return null;
    }
    
    /**
     * Try to pass the request to a child element. If none is found return null
     * @param string $name
     * @param array $path
     * @return NULL
     * 
     * @wiki /Writing_own_property_classes#InfoMarket_interaction
     */
    protected function passItemRequest(string $name, array $path)
    {
        return null;
    }
    
    /**
     * When no path elements are left return $this, if only one is left check for
     * terminal item (pseudo child, see requestTerminalItem. Otherwise try to pass
     * The request to a child.
     * @param array $path
     * @return Sunhill\\Properties\Property|NULL
     * 
     * @wiki /Properties#InfoMarket_interaction
     */
    public function requestItem(array $path)
    {
        if (empty($path)) {
            return $this;
        }
        $next = array_shift($path);
        if (empty($path) && ($result = $this->requestTerminalItem($next))) {
            return $result;
        }
        return $this->passItemRequest($next, $path);
    }
    
    // ================================= Additional Fields ======================================
    /**
     * Properties get the possibility to add additinal fields (like property->set_additional)
     */
    private $additional_fields = [];
    
    /**
     * Extends the property with the possibility to deal with additional getters and setters
     *
     * @param string $method
     * @param array $params
     * @return mixed|NULL|Sunhill\\Properties\Property
     *
     * Test: /Unit/Properties/PropertyTest::testAdditionalGetter
     * Test: /Unit/Properties/PropertyTest::testUnknownMethod
     */
    public function __call(string $method, array $params)
    {
        if (substr($method,0,4) == 'get_') {
            $name = strtolower(substr($method,4));
            if (isset($this->additional_fields[$name])) {
                return $this->additional_fields[$name];
            } else {
                return null;
            }
        } else if (substr($method,0,4) == 'set_') {
            $name = strtolower(substr($method,4));
            $this->additional_fields[$name] = $params[0];
            return $this;
        }
        throw new PropertyException(static::class.": Unknown method '$method' called");
    }
    
    
    /**
     * Returns a blank property with the given name or type
     * 
     * @param string $name
     * @param string $type_or_semantic
     * @return AbstractProperty
     */
    protected function createProperty(string $type_or_semantic = 'string', string $name = ''): AbstractProperty
    {
        if (!Properties::isPropertyRegistered($type_or_semantic)) {
            throw new InvalidTypeOrSemanticException("The given '$type_or_semantic' is not registered");          
       }
       $namespace = Properties::getNamespaceOfProperty($type_or_semantic);
       $property = new $namespace();
       if (!empty($name)) {
            $property->setName($name);
       }
       
       return $property;
    }
    
    // ================================== Infos ===============================================
    /**
     * Stores the collection infos
     * @var unknown
     */
    protected static $infos;
    
    /**
     * Creates an empty array for infos and calls setupInfos()
     * Infos are class wide additional informations that are stored to a class. Useful for information
     * like name, table-name, editable, etc.
     * Test: /Unit/Objects/PropertyCollection_infoTest
     * 
     * @wiki /Writing_own_property_classes#Information
     */
    protected static function initializeInfos()
    {
        static::$infos = [];
        static::setupInfos();
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     * 
     * @wiki /Writing_own_property_classes#Information
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'AbstractProperty');
        static::addInfo('description', 'A base class for a property.', true);
    }
    
    /**
     * Adds an entry to the class definition
     * @param string $key: The key for the piece of information
     * @param unknown $value: The value of this information
     * @param bool $translatable: A boolean that indicates, if the return should pass the __() function
     * Test: /Unit/Objects/PropertyCollection_infoTest
     * 
     * @wiki /Writing_own_property_classes#Information
     */
    protected static function addInfo(string $key, $value, bool $translatable = false)
    {
        $info = new \StdClass();
        $info->key = $key;
        $info->value = $value;
        $info->translatable = $translatable;
        static::$infos[$key] = $info;
    }
    
    /**
     * returns the Information named $key
     * @param string $key
     * @throws PropertiesCollectionException
     * @return string|array|NULL|unknown
     * Test: /Unit/Objects/PropertyCollection_infoTest
     * 
     * @wiki /Properties#Information
     */
    public static function getInfo(string $key, $default = null)
    {
        static::initializeInfos();
        if (!isset(static::$infos[$key])) {
            if (is_null($default)) {
                throw new PropertyKeyDoesntExistException("The key '$key' is not defined.");
            } else {
                return $default;
            }
        }
        $info = static::$infos[$key];
        if ($info->translatable) {
            return static::translate($info->value);
        } else {
            return $info->value;
        }
    }
    
    /**
     * Return all avaiable infos
     * @return unknown
     *
     * Test: /unit/Ovhects/PropertyCollection_infoTest
     * 
     * @wiki /Properties#Information
     */
    public static function getAllInfos()
    {
        static::initializeInfos();
        return static::$infos;
    }
    
    /**
     * Checks if the given info is defined
     * @param string $key
     * @return bool
     * Test: /unit/Ovhects/PropertyCollection_infoTest
     * 
     * @wiki /Properties#Information
     */
    public static function hasInfo(string $key): bool
    {
        static::initializeInfos();
        return isset(static::$infos[$key]);
    }
    
    /**
     * Wrapper for the __() function
     * @param unknown $info
     * @return string|array|NULL
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function translate(string $info): string
    {
        return __($info);
    }
    
    const EQUALITY = ['=','==','<>','!=','in','notin'];
    const SIZE = ['<','<=','>','>=','between'];
    const WITHNULL = ['isnull','isnotnull'];
    /**
     * A static array that lists all allowed relations for this property
     * @var array
     * 
     * @wiki Writing_own_properties#Relations
     */
    protected static $allowed_relations = [];
    
    /**
     * Returns the static variable $allowed_relations
     * 
     * @return array
     * 
     * @wiki Writing_own_properties#Relations
     */
    public static function getAllowedRelations(): array
    {
        return static::$allowed_relations;
    }
    
    /**
     * Tests if the given relation is in the $allowed_relations array
     * 
     * @param string $relation
     * @return bool
     * 
     * @wiki Writing_own_properties#Relations
     */
    public static function isAllowedRelation(string $relation): bool
    {
        return in_array($relation, static::getAllowedRelations());
    }

    private function hasParents() 
    {
        return (bool)class_parents($this);
    }

    /**
     * Tests if the relation is allowed. If yes it test if the relation is true
     * 
     * @param string $relation
     * @param unknown $compare
     * @return bool
     * 
     * @wiki Writing_own_properties#Relations
     */
    public function testRelation(string $relation, $compare): bool
    {
        if (!static::isAllowedRelation($relation)) {
            throw new NotAllowedRelationException("The relation '$relation' is not allowed for this property.");
        }
       return $this->doTestRelation($relation, $compare);
    }
    
    private function testIn($values): bool
    {
        if (is_scalar($values)) {
            $values = [$values];
        }
        if (!is_array($values)) {
            throw new WrongTypeException("The given type is not expected.");
        }
        $own_value = $this->getValue();
        foreach ($values as $value) {
            if ($value == $own_value) {
                return true;
            }
        }
        return false;
    }
    
    protected function doTestRelation(string $relation, $compare): ?bool
    {
        switch ($relation) {
            case '=':
            case '==':
                return $this->getValue() == $compare;
            case '!=':
            case '<>':
                return $this->getValue() != $compare;
            case 'in':
                return $this->testIn($compare);
            case 'notin':
                return !$this->testIn($compare);
            case '<':
                return $this->getValue() < $compare;
            case '<=':
                return $this->getValue() <= $compare;
            case '>':
                return $this->getValue() > $compare;
            case '>=':
                return $this->getValue() >= $compare;
            case 'isnull':
                return is_null($this->getValue());
            case 'isnotnull':
                return !is_null($this->getValue());
        }
    }
    
}