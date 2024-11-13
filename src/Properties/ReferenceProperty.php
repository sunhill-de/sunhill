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
    
    public function getAccessType(): string
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
    
}