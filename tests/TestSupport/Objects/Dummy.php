<?php

namespace Sunhill\Tests\TestSupport\Objects;

use Sunhill\Objects\ORMObject;
use Sunhill\Types\TypeInteger;
use Sunhill\Properties\ElementBuilder;

class Dummy extends ORMObject
{
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $builder->addProperty(TypeInteger::class,'dummyint');
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'Dummy');
        static::addInfo('description', 'A simple object with only one integer member.', true);
        static::addInfo('storage_id', 'dummies');
    }
    
}