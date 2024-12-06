<?php
/**
 * @file SHA1.php
 * A semantic class for an sha1 string 
 * Lang en
 * Reviewstatus: 2024-10-09
 * Localization: complete
 * Documentation: complete
 * Tests: Unit/Semantic/SemanticTest.php
 * Coverage: unknown
 */

namespace Sunhill\Semantics;

class SHA1 extends IDString
{
    
    /**
     * Returns the unique id string for the semantic of this property
     *
     * @return string
     */
    public static function getSemantic(): string
    {
        return 'md5';
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
     * Checks if the given string is a valid sha1 string
     *
     * {@inheritDoc}
     * @see Sunhill\\\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        return preg_match('/^[a-f0-9]{40}$/', $input);
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'sha1');
        static::addInfo('description', 'The SHA1 hash of something.', true);
        static::addInfo('type', 'semantic');
    }
    
}