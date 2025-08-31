<?php

/**
 * @file NetworkAddress.php
 * A semantic class for a string that is the address of a network device
 * Lang en
 * Reviewstatus: 2024-10-09
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage Unit: 0 % (2025-06-06)
 */

namespace Sunhill\Semantics;

use Sunhill\Types\TypeVarchar;

class NetworkAddress extends TypeVarchar
{
    /**
     * Returns the unique id string for the semantic of this property
     */
    public static function getSemantic(): string
    {
        return 'network_address';
    }

    /**
     * Returns some keywords to the current semantic
     */
    public static function getSemanticKeywords(): array
    {
        return ['network'];
    }
}
