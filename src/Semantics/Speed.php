<?php
/**
 * @file Speed.php
 * Defines a float that represents the speed
 * Lang de,en
 * Reviewstatus: 2024-10-09
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Semantics;

use Sunhill\Types\TypeFloat;

class Speed extends TypeFloat
{
    
    /**
     * Returns the unique id string for the semantic of this property
     *
     * @return string
     */
    public static function getSemantic(): string
    {
        return 'speed';
    }
    
    /**
     * Returns some keywords to the current semantic
     *
     * @return array
     */
    public static function getSemanticKeywords(): array
    {
        return ['speed'];
    }
    
    /**
     * Returns the unique id string for the unit of this property
     *
     * @return string
     */
    public static function getUnit(): string
    {
        return 'meterpersecond';
    }
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'speed');
        static::addInfo('description', 'The speed of something.', true);
        static::addInfo('type', 'semantic');
    }
    
}