<?php

/**
 * @file AbstractInteger.php
 * Defines a type to integers
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Properties\Types;

class TypeInteger extends TypeNumeric
{
   
    protected function isNumericType($input): bool
    {
        return (ctype_digit($input) || is_int($input));
    }
    
    public function getAccessType(): string
    {
        return 'integer';
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'integer');
        static::addInfo('description', 'The basic type integer.', true);
        static::addInfo('type', 'basic');
    }
    
}