<?php

/**
 * @file AbstractType.php
 * Defines a basic validator for types
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Properties\Types;

use Sunhill\Properties\Exceptions\InvalidParameterException;
use Sunhill\Properties\Properties\AbstractSimpleProperty;

class TypeVarchar extends AbstractSimpleProperty
{
   
    /**
     * Indicates how long the string may be
     * 
     * @var integer
     */
    protected $max_length = 255;
    
    /**
     * Indicates what should happen, when the given string exceeds the defined max length
     * Could be:
     * - cut = just cut the string
     *   invalid = declare this string as invalid
     *   
     * @var string
     */
    protected $length_exceed_policy = 'cut';
    
    /**
     * Setter for max_length
     * 
     * @param int $max_length
     * @return Sunhill\\Types\TypeVarchar
     */
    public function setMaxLen(int $max_length)
    {
        $this->max_length = $max_length;
        return $this;
    }
    
    /**
     * Getter for max_length
     * 
     * @return int
     */
    public function getMaxLen(): int
    {
        return $this->max_length;    
    }
    
    /**
     * Setter for length_exceed_policy
     * 
     * @param string $policy
     * @return Sunhill\\Types\TypeVarchar
     * @throws InvalidParameterException when $policy is not cur or invalid
     */
    public function setLengthExceedPolicy(string $policy)
    {
        if (($policy <> 'cut') && ($policy <> 'invalid')) {
           throw new InvalidParameterException("LengthExceedPolicy may only be 'cut' or 'invalid'"); 
        }
        
        $this->length_exceed_policy = $policy;
        
        return $this;
    }
    
    /**
     * Getter for length_exceed_policy
     * 
     * @return string
     */
    public function getLengthExceedPolicy(): string
    {
        return $this->length_exceed_policy;        
    }
    
    /**
     * When length_exceed_policy is cut always return true otherwise false when the given string 
     * is loinger than max_lenbgt
     * 
     * {@inheritDoc}
     * @see Sunhill\\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        return (is_scalar($input) && (($this->length_exceed_policy <> 'invalid') || (strlen($input) <= $this->max_length)));
    }
    
    /**
     * Cuts the input string to a maximum length
     * 
     * {@inheritDoc}
     * @see Sunhill\\ValidatorBase::doConvertToInput()
     */
    protected function formatForStorage($input)
    {
        return substr($input,0,$this->max_length);
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
        static::addInfo('name', 'varchar');
        static::addInfo('description', 'The basic type varchar.', true);
        static::addInfo('type', 'basic');
    }
    
}