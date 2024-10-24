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
     * Storages the elements of this record as an associative array
     * @var array
     */
    protected array $elements = [];
    
    /**
     * Stores the structure of the elements of this elements as an associative array
     * @var array
     */
    protected array $elements_structure = [];
    
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
    
    public function appendElement(mixed $element, ?string $name = null, string $inclusion = 'include', $storage = null)
    {
        $element = $this->getElementProperty($element);
        
        return $element;
    }
}