<?php
/**
 * @file EmbedChildProperty.php
 * Type: Test support file
 */

namespace Sunhill\Tests\Feature\NonDatabaseTests\Properties\PooledRecordProperty\Examples;

use Sunhill\Properties\ElementBuilder;

class EmbedChildProperty extends EmbedParentProperty
{
  
    public static function getStorageID(): string
    {
        return 'poolB'; // Per default nothing
    }
    
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->string('child_str');
    }
        
}