<?php
/**
 * @file Airpressure.php
 * Defines a float that represents the air pressure (derrived of pressure) 
 * Lang de,en
 * Reviewstatus: 2024-10-08
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Semantics;

use Sunhill\Types\TypeFloat;

class Airpressure extends Pressure
{
    
    /**
     * Returns the unique id string for the semantic of this property
     *
     * @return string
     */
    public static function getSemantic(): string
    {
        return 'airpressure';
    }
    
    /**
     * Returns some keywords to the current semantic
     *
     * @return array
     */
    public static function getSemanticKeywords(): array
    {
        return ['pressure','weather'];
    }
 
    /**
     * Returns the unique id string for the unit of this property
     *
     * @return string
     */
    public static function getUnit(): string
    {
        return 'hectopascal';
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'airpressure');
        static::addInfo('description', 'The airpressure.', true);
        static::addInfo('type', 'semantic');
    }
    
}