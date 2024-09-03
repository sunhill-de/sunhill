<?php

/**
 * @file TypeEnum.php
 * Defines a type for enums
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Types;

use Sunhill\Properties\AbstractSimpleProperty;

class TypeEnum extends AbstractSimpleProperty
{
   
    /**
     * Indicates how long the string may be
     * 
     * @var integer
     */
    protected $allowed_values = [];
    
    /**
     * Setter for allowed_values
     * 
     * @param int $enum_values
     * @return Sunhill\\Types\TypeVarchar
     */
    public function setEnumValues(array $enum_values)
    {
        $this->allowed_values = $enum_values;
        return $this;
    }
    
    /**
     * Getter for allowed_values
     * 
     * @return int
     */
    public function getEnumValues(): array
    {
        return $this->allowed_values;    
    }
    
    /**
     * Checks if the given input value is in allowed_values
     * 
     * {@inheritDoc}
     * @see Sunhill\\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        return in_array($input, $this->allowed_values);
    }
    
    public function getAccessType(): string
    {
        return 'string';
    }
     
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'enum');
        static::addInfo('description', 'The basic type enum.', true);
        static::addInfo('type', 'basic');
    }
    
}