<?php

/**
 * @file RecordProperty.php
 * Defines a property as a base for all record like properties
 * Lang en
 * Reviewstatus: 2024-10-24
 * Localization: complete
 * Documentation: completeF
 * Tests:
 * Coverage Unit:96.55 % (2025-06-06)
 *
 * Wiki: /RecordProperties
 * tests /tests/Unit/Properties/RecordProperties/*
 */

namespace Sunhill\Properties;

use Sunhill\Facades\Properties;
use Sunhill\Properties\Exceptions\NotAllowedInclusionException;
use Sunhill\Properties\Exceptions\PropertyAlreadyInListException;
use Sunhill\Properties\Exceptions\PropertyHasNoNameException;
use Sunhill\Properties\Exceptions\PropertyNameAlreadyGivenException;
use Sunhill\Properties\Exceptions\PropertyNotFoundException;
use Sunhill\Storage\AbstractStorage;

class RecordProperty extends AbstractProperty implements \Countable, \Iterator
{
    /**
     * Stores how this record should treat inherited elements
     *
     * @var string
     */
    protected static $inherited_inclusion = 'include';

    protected $skipping_members = [];

    public function __construct($elements = null)
    {
        parent::__construct();
        $this->initializeInheritance();

        if (is_callable($elements)) {
            $this->setupRecord($elements);
        }

        if (! is_null($storage = $this->getStorage())) {
            $storage->setStructure($this->getStructure());
        }
    }

    private function addMemebers(ElementBuilder $builder, ?string $storage_id)
    {
        $members = $builder->getElements();
        foreach ($members as $name => $property) {
            $this->appendElement($property, $name, (static::$inherited_inclusion == 'embed') ? $storage_id : static::getStorageID());
        }
    }

    private function addIncludes(ElementBuilder $builder) {}

    /**
     * Is called by
     *  a) the constructor when a callable $elements parameter is passed
     *  b) by initializeInheritance() for every ancestor
     *  c) directly by the initializing structure
     *
     * @param  RecordProperty  $target
     */
    public function setupRecord(callable $elements, ?string $storage_id = null): static
    {
        $element_builder = new ElementBuilder;
        $elements($element_builder);
        $this->addMemebers($element_builder, $storage_id);
        $this->addIncludes($element_builder);

        return $this;
    }

    protected static function initializeRecord(ElementBuilder $builder) {}

    private function initializeChild(string $class)
    {
        $this->setupRecord([$class, 'initializeRecord'], (static::$inherited_inclusion == 'include') ? static::getStorageID() : $class::getStorageID());
    }

    private function definesOwnProperties($pointer): bool
    {
        $class = new \ReflectionClass($pointer);
        $method = $class->getMethod('initializeRecord');

        return $method->class == $pointer;
    }

    /**
     * This method is callen whenever the class defines no own properties (= skipping record)
     */
    protected function handleSkippingRecord(string $pointer) {}

    private function initializeInheritance()
    {
        $pointer = $this::class;
        while ($pointer !== RecordProperty::class) {
            if ($this->definesOwnProperties($pointer)) {
                $this->initializeChild($pointer);
            } else {
                $this->handleSkippingRecord($pointer);
            }
            $pointer = (static::$inherited_inclusion == 'none') ? RecordProperty::class : get_parent_class($pointer);
        }
    }

    public static function getAccessType(): string
    {
        return 'record';
    }

    public function isValid($input): bool
    {
        return true;
    }

    /**
     * Overwrites the inherited setStorage method to pass the new storage
     * to all of the elements.
     *
     * {@inheritDoc}
     *
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
     */
    public static function getStorageID(): string
    {
        return ''; // Per default nothing
    }

    /**
     * Storages the elements of this record as an associative array
     */
    protected array $elements = [];

    /**
     * Stores the structure of the elements of this elements as an associative array
     */
    protected array $elements_structure = [];

    /**
     * Checks if $name is set, if yes set it to the element
     *
     * @param  AbstractProperty  $propery
     */
    private function writeName(AbstractProperty $property, ?string $name)
    {
        if (! empty($name)) {
            $property->setName($name);
        }
        if (empty($property->getName())) {
            throw new PropertyHasNoNameException('The property has no name');
        }
    }

    /**
     * Checks if the name is alread given
     */
    private function checkForDuplicateName(AbstractProperty $property)
    {
        if (array_key_exists($property->getName(), $this->elements)) {
            throw new PropertyNameAlreadyGivenException("The name '".$property->getName()."' is already in use");
        }
    }

    /**
     * Checks if the propeery was already appended
     */
    private function checkForDuplicateProperty(AbstractProperty $property)
    {
        if (in_array($property, array_values($this->elements))) {
            throw new PropertyAlreadyInListException("The property '".$property->getName()."' is already in this record");
        }
    }

    private function linkElement(AbstractProperty $property)
    {
        $property->setOwner($this);
        if (! empty($this->getStorage())) {
            $property->setStorage($this->getStorage());
        }
    }

    /**
     * Adds the structure of the property to the structures list
     */
    private function appendToElementsAndStructures(AbstractProperty $property, mixed $storage_id)
    {
        $this->elements[$property->getName()] = $property;
        $this->elements_structure[$property->getName()] = $storage_id ?? static::getStorageID();
    }

    private function checkElement(AbstractProperty $element)
    {
        if (is_a($element, RecordProperty::class)) {
            throw new NotAllowedInclusionException("Record must't be added by appendElement()");
        }
    }

    public function appendElement(AbstractProperty $element, ?string $name = null, $storage_id = null)
    {
        $this->checkElement($element);

        $this->writeName($element, $name);
        $this->checkForDuplicateName($element);
        $this->checkForDuplicateProperty($element);

        $this->linkElement($element);
        $this->appendToElementsAndStructures($element, $storage_id);

        return $element;
    }

    /**
     * Checks if a property element with the given name exists.
     */
    public function hasElement(string $name): bool
    {
        return array_key_exists($name, $this->elements);
    }

    /**
     * When an element with this name exists, return it's property object otherwise return null
     */
    public function getElement(string $name): ?AbstractProperty
    {
        if ($this->hasElement($name)) {
            return $this->elements[$name];
        }

        return null;
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
        if (! $this->hasElement($varname)) {
            return $this->handleGetUnkownElement($varname);
        }
        $property = $this->elements[$varname];
        if (is_a($property, ArrayProperty::class)) {
            return $property;
        } else {
            return $property->getValue();
        }
    }

    protected function handleGetUnkownElement(string $varname)
    {
        throw new PropertyNotFoundException("The property '$varname' does not exist.");
    }

    public function __set($varname, $value)
    {
        if (! $this->hasElement($varname)) {
            $this->handleSetUnknownElement($varname, $value);
        }
        $property = $this->elements[$varname];

        return $property->setValue($value);
    }

    protected function handleSetUnknownElement(string $varname, $value)
    {
        throw new PropertyNotFoundException("The property '$varname' does not exist.");
    }

    private function getElements(): array
    {
        $result = [];
        foreach ($this->elements as $key => $element) {
            $result[$key] = $element->getStructure();
            $result[$key]->storage_subid = $this->elements_structure[$key];
        }

        return $result;
    }

    /**
     * Modifies the inhertied function to include the elements
     *
     * {@inheritDoc}
     *
     * @see \Sunhill\Properties\AbstractProperty::getStructure()
     */
    public function getStructure(): \stdClass
    {
        $return = parent::getStructure();
        $return->name = static::getInfo('storage_id', '');
        $return->elements = $this->getElements();
        $return->options = static::getAllInfos();
        $return->skipping_members = $this->skipping_members;

        return $return;
    }
}
