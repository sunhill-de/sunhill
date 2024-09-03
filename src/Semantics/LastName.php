<?php
/**
 * @file LastName.php
 * Defines a derived name that represents a last name of a person or aninmal
 * Lang de,en
 * Reviewstatus: 2024-03-01
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Properties\Semantics;

use Sunhill\Properties\Types\TypeVarchar;

class LastName extends Name
{
    
    /**
     * Returns the unique id string for the semantic of this property
     *
     * @return string
     */
    public function getSemantic(): string
    {
        return 'last_name';
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'lastname');
        static::addInfo('description', 'The last name of a person.', true);
        static::addInfo('type', 'semantic');
    }
    
}