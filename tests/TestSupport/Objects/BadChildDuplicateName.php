<?php

namespace Sunhill\Tests\TestSupport\Objects;

use Sunhill\Properties\ElementBuilder;
use Sunhill\Types\TypeInteger;

class BadChildDuplicateName extends Dummy
{
    
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'dummyint');
    }
    
}