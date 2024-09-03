<?php

/**
 * @file TypeFloat.php
 * Defines a type for floats
 * Lang en
 * Reviewstatus: 2024-02-28
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Properties\Types;

class TypeFloat extends TypeNumeric
{
   
    /**
     * Tells getHumanValue how many digits after the comma should be diaplayed
     * 
     * @var integer
     */
    protected $precision = 2;
    
    public function setPrecision(int $digits)
    {
       $this->precision = $digits; 
    }
    
    /**
     * Getter for precision 
     * @return int
     */
    public function getPrecision(): int
    {
        return $this->precision;    
    }
    
    protected function isNumericType($input): bool
    {
        return is_numeric($input);
    }

    public function getAccessType(): string
    {
        return 'float';
    }
    
    protected function formatForHuman($input)
    {
        return parent::formatForHuman(round($input, $this->precision));
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'float');
        static::addInfo('description', 'The basic type float.', true);
        static::addInfo('type', 'basic');
    }
    
}