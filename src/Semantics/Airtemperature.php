<?php

/**
 * @file Temperature.php
 * Defines a float that represents the temperature of something
 * Lang de,en
 * Reviewstatus: 2024-10-08
 * Localization: complete
 * Documentation: complete
 * Tests:
 * Coverage Unit: 60 % (2025-06-06)
 */

namespace Sunhill\Semantics;

class Airtemperature extends Temperature
{
    /**
     * Returns the unique id string for the semantic of this property
     */
    public static function getSemantic(): string
    {
        return 'airtemperature';
    }

    /**
     * Returns some keywords to the current semantic
     */
    public static function getSemanticKeywords(): array
    {
        return ['temperature', 'weather'];
    }

    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'airtemperature');
        static::addInfo('description', 'The air temperature (weather).', true);
        static::addInfo('type', 'semantic');
    }
}
