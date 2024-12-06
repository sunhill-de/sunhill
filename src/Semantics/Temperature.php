<?php
/**
 * @file Temperature.php
 * Defines a float that represents the temperature of something 
 * Lang de,en
 * Reviewstatus: 2024-10-09
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Semantics;

use Sunhill\Types\TypeFloat;

class Temperature extends TypeFloat
{
    
    /**
     * Returns the unique id string for the semantic of this property
     *
     * @return string
     */
    public static function getSemantic(): string
    {
        return 'temperature';
    }
    
    /**
     * Returns some keywords to the current semantic
     *
     * @return array
     */
    public static function getSemanticKeywords(): array
    {
        return ['temperature'];
    }
 
    /**
     * Returns the unique id string for the unit of this property
     *
     * @return string
     */
    public static function getUnit(): string
    {
        return 'degreecelsius';
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'temperature');
        static::addInfo('description', 'The temperature of something.', true);
        static::addInfo('type', 'semantic');
    }
    
}