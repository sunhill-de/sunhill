<?php

/**
 * @file URL.php
 * A semantic class for an url
 * Lang en
 * Reviewstatus: 2024-10-09
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage Unit: 66.67 % (2025-06-06)
 */

namespace Sunhill\Semantics;

class URL extends IDString
{
    /**
     * Returns the unique id string for the semantic of this property
     */
    public static function getSemantic(): string
    {
        return 'url';
    }

    /**
     * Returns some keywords to the current semantic
     */
    public static function getSemanticKeywords(): array
    {
        return ['id', 'computer'];
    }

    /**
     * Checks if the given string is a valid email address
     *
     * {@inheritDoc}
     *
     * @see Sunhill\\\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        return filter_var($input, FILTER_VALIDATE_URL);
    }

    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'url');
        static::addInfo('description', 'A internet url.', true);
        static::addInfo('type', 'semantic');
    }
}
