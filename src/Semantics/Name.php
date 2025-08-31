<?php

/**
 * @file Name.php
 * Defines a derived varchar that represents a name
 * Lang de,en
 * Reviewstatus: 2024-10-09
 * Localization: complete
 * Documentation: complete
 * Tests:
 * Coverage Unit: 60 % (2025-06-06)
 */

namespace Sunhill\Semantics;

use Sunhill\Types\TypeVarchar;

class Name extends TypeVarchar
{
    /**
     * Returns the unique id string for the semantic of this property
     */
    public static function getSemantic(): string
    {
        return 'name';
    }

    /**
     * Returns some keywords to the current semantic
     */
    public static function getSemanticKeywords(): array
    {
        return ['name'];
    }

    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'name');
        static::addInfo('description', 'The name of something.', true);
        static::addInfo('type', 'semantic');
    }
}
