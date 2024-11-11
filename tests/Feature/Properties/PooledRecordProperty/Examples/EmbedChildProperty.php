<?php

namespace Sunhill\Tests\Feature\Properties\PooledRecordProperty\Examples;

use Sunhill\Properties\PooledRecordProperty;
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