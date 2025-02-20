<?php
/**
 * @file Count.php
 * A semantic class that represents the count of something
 * Lang en
 * Reviewstatus: 2024-10-08
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage: unknown
 */

namespace Sunhill\Semantics;

use Sunhill\Types\TypeInteger;

class Count extends TypeInteger
{
 
    public function __construct()
    {
        $this->setMinimum(0);
    }
    
    /**
     * Returns the unique id string for the semantic of this property
     *
     * @return string
     */
    public static function getSemantic(): string
    {
        return 'count';
    }
    
    /**
     * Returns some keywords to the current semantic
     *
     * @return array
     */
    public static function getSemanticKeywords(): array
    {
        return ['count'];
    }
     
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'count');
        static::addInfo('description', 'The count of items.', true);
        static::addInfo('type', 'semantic');
    }
    
}