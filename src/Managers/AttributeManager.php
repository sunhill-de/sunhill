<?php
/**
 * @file AttributeManager.php
 * Provides the AttributeManager object for accessing information about attributes.
 *
 * @author Klaus Dimde
 * ----------------------------------------------------------------------
 * Lang en
 * Reviewstatus: 2024-12-11
 * Localization: unknown
 * Documentation:
 * Wiki:  
 * Tests: Unit/Managers/AttributeManagerTest.php
 * Coverage: 
 */

namespace Sunhill\Managers;

use Sunhill\Basic\Base;
use Sunhill\Query\BasicQuery;

class AttributeManager extends Base
{
    
    /**
     * Returns a query for attributes
     * 
     * @param ?string $attribute_name (either the name of a attribute or (if null) a query on attributes at all)
     * @return BasicQuery
     */
    public function query(?string $attribute_name = null): BasicQuery
    {
        
    }
    
    public function registerAttribute($attribute)
    {
        
    }
    
}