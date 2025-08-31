<?php

/**
 * @file Age.php
 * A semantic class for describing the age of a person or thing
 * Lang en
 * Reviewstatus: 2024-10-08
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage Unit: 66.67 % (2025-06-06)
 */

namespace Sunhill\Semantics;

use Sunhill\Types\TypeInteger;

class Age extends TypeInteger
{
    /**
     * Returns the unique id string for the semantic of this property
     */
    public static function getSemantic(): string
    {
        return 'age';
    }

    /**
     * Returns some keywords to the current semantic
     */
    public static function getSemanticKeywords(): array
    {
        return ['time'];
    }

    /**
     * Returns the unique id string for the unit of this property
     */
    public static function getUnit(): string
    {
        return 'second';
    }

    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'age');
        static::addInfo('description', 'The age of a person, thing, etc.', true);
        static::addInfo('type', 'semantic');
    }
}
