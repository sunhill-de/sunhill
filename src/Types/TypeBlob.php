<?php

/**
 * @file AbstractType.php
 * Defines a basic validator for types
 * Lang en
 * Reviewstatus: 2024-02-05
 * Localization: complete
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 */

namespace Sunhill\Types;

use Sunhill\Properties\AbstractSimpleProperty;

class TypeBlob extends AbstractSimpleProperty
{
   
    /**
     * Is only has to be scalar 
     * 
     * {@inheritDoc}
     * @see Sunhill\\ValidatorBase::isValid()
     */
    public function isValid($input): bool
    {
        return is_scalar($input);
    }

    public function getAccessType(): string
    {
        return 'blob';
    }
    
    /**
     * This method must be overwritten by the derrived class to define its infos
     * Test: /Unit/Objects/PropertyCollection_infoTest
     */
    protected static function setupInfos()
    {
        static::addInfo('name', 'blob');
        static::addInfo('description', 'The basic type blob.', true);
        static::addInfo('type', 'basic');
    }
        
}