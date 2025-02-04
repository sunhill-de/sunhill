<?php
/**
 * @file ReferenceProperty.php
 * Defines a property that is a reference to a RecordProperty
 * Lang en
 * Reviewstatus: 2024-11-07
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: 67.86% (2024-11-13)
 *
 * Wiki: 
 * tests 
 */

namespace Sunhill\Properties;

class ReferenceProperty extends AbstractProperty 
{
   
    /**
     * Stores the types of record properties that are allowed for this reference
     * 
     * @var array
     */
    protected $allowed_properties = [];
    
    protected $refered_record;
    
    /**
     * First checks if it is a record property at all, then if it is in the list of 
     * allowed properties (or the list is empty)
     * 
     * {@inheritDoc}
     * @see \Sunhill\Properties\AbstractProperty::isValid()
     */
    public function isValid($input): bool
    {
        if (!is_a($input, RecordProperty::class)) {
            return false;
        }
        if (empty($this->allowed_properties)) {
            return true;
        }
        foreach ($this->allowed_properties as $allowed_property) {
            if (is_a($input, $allowed_property)) {
                return true;
            }
        }
        return false;
    }
    
    public static function getAccessType(): string
    {
        return 'record';
    }
    
    private function handleArray(array $allowed_properties)
    {
        foreach ($allowed_properties as $allowed_property) {
            $this->handleString($allowed_property);
        }
    }
    
    private function handleString(string $allowed_property)
    {
        if (class_exists($allowed_property)) {
            $this->allowed_properties[] = $allowed_property;
            return;
        }
        if ($namespace = Properties::getNamespaceOfProperty($allowed_property)) {
            $this->allowed_properties[] = $namespace;
            return;            
        }
        throw new PropertyNotFoundException("The property '$allowed_property' was not found");
    }
    
    /**
     * Sets the allowed types for this property.
     * 
     * @param array|string $allowed_proprty When it is a string every entry is allowed otherwise 
     * only the one
     * 
     * @return static
     */
    public function setAllowedProperty(array|string $allowed_proprty): static
    {
        $this->allowed_properties = [];
        if (is_array($allowed_proprty)) {
            $this->handleArray($allowed_proprty);
        } else {
            $this->handleString($allowed_proprty);
        }
        return $this;
    }
    
    protected function handleUninitialized()
    {
        if (count($this->allowed_properties) == 1) {
            $result = new $this->allowed_properties[0]();
            $this->getStorage()->setValue($this->getName(), $result);
            return $result;
        }
        return parent::handleUninitialized();
    }

    protected function formatForStorage($value)
    {
        if (is_a($value, PooledRecordProperty::class)) {
            $this->refered_record = $value;
            return $value->getID();
        } else {
            return $value;       
        }
    }
    
    /**
     * We received an ID from the storage so we try to load the referenced record
     * 
     * @param unknown $id
     */
    protected function tryToLoadRecord($id)
    {
        foreach ($this->allowed_properties as $property) {
            if (method_exists($property, 'IDexists')) {
                $test = new $property();
                if ($test->IDexists($id)) {
                    $test->load($id);
                    $this->refered_record = $test;
                    return $this->refered_record;
                }
            }
        }
    }
    
    /**
     * When dealing with pooled records the storage only stores the id of the record. So in this method we have
     * to convert the id to a record. 
     * 
     * {@inheritDoc}
     * @see \Sunhill\Properties\AbstractProperty::formatFromStorage()
     */
    protected function formatFromStorage($value)
    {
        if (isset($this->refered_record)) {
            // A record is already referenced
            return $this->refered_record;
        } else if (is_scalar($value)) {
            // We assume an ID
            return $this->tryToLoadRecord($value);
        }
        return $value; // In all other cases we assume a non pooled record and return whatever the storage returns
    }
    
    public function getID()
    {
        if (isset($this->refered_record)) {
           return $this->refered_record->getID(); 
        } else {
            return $this->getValue();
        }
    }
}