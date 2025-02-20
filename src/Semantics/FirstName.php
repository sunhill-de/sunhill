<?php
/**
 * @file FirstName.php
 * Defines a derived name that represents a first name of a person or aninmal
 * Lang de,en
 * Reviewstatus: 2024-10-09
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Semantics;

use Sunhill\Types\TypeVarchar;

class FirstName extends Name
{
    
    /**
     * Returns the unique id string for the semantic of this property
     *
     * @return string
     */
    public static function getSemantic(): string
    {
        return 'first_name';
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'firstname');
        static::addInfo('description', 'The first name of a person or animal.', true);
        static::addInfo('type', 'semantic');
    }
    
}