<?php
/**
 * @file UUID4.php
 * A semantic class for an uuid version 4 
 * Lang en
 * Reviewstatus: 2024-10-09
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage: unknown
 */

namespace Sunhill\Semantics;

class UUID4 extends IDString
{
    
    /**
     * Returns the unique id string for the semantic of this property
     *
     * @return string
     */
    public static function getSemantic(): string
    {
        return 'uuid4';
    }
    
    /**
     * Returns some keywords to the current semantic
     *
     * @return array
     */
    public static function getSemanticKeywords(): array
    {
        return ['id','computer'];
    }
  
    /**
     * The storage stores a mac address in lower case
     *
     * @param unknown $input
     * @return unknown, by dafult just return the value
     */
    protected function formatForStorage($input)
    {
        return strtolower($input);
    }
    
    /**
     * Checks if the given string is a valid email address
     *
     * {@inheritDoc}
     * @see Sunhill\\\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        if (!is_string($input) || (preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $input) !== 1)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'uuid');
        static::addInfo('description', 'An uuid of something.', true);
        static::addInfo('type', 'semantic');
    }
    
}