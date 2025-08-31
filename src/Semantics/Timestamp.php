<?php

/**
 * @file Timestamp.php
 * A semantic class for a timestamp
 * Lang en
 * Reviewstatus: 2024-10-09
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage Unit: 66.67 % (2025-06-06)
 */

namespace Sunhill\Semantics;

use Sunhill\Types\TypeDateTime;

class Timestamp extends TypeDateTime
{
    /**
     * Returns the unique id string for the semantic of this property
     */
    public static function getSemantic(): string
    {
        return 'timestamp';
    }

    /**
     * Returns some keywords to the current semantic
     */
    public static function getSemanticKeywords(): array
    {
        return ['time'];
    }

    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'timestamp');
        static::addInfo('description', 'A timestamp.', true);
        static::addInfo('type', 'semantic');
    }
}
