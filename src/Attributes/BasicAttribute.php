<?php

/**
 * @file BasicAttribute.php
 * Provides the basic class for attributes
 * Lang en
 * Reviewstatus: 2024-12-09
 * Localization: complete
 * Documentation: complete
 *
 * Tests: Unit/Attributes/BasicAttributeTest.php
 * Coverage: 
 */

namespace Sunhill\Attributes;

use Sunhill\Storage\PersistentPoolStorage;
use Sunhill\Query\BasicQuery;

class BasicAttribute extends PersistentPoolStorage
{
  
    public static $attribute_name = '';
    
    protected function calculateStorageName(): string
    {
        return 'attribute_'.strtolower(static::$attribute_name);    
    }
    
    protected function doCommitNew()
    {
        
    }
    
    protected function doDelete(mixed $id)
    {
        
    }
    
    protected function doCommitLoaded()
    {
        
    }
    
    protected function doLoad(mixed $id)
    {
        
    }
    
    protected function doQuery(): BasicQuery
    {
        
    }

}