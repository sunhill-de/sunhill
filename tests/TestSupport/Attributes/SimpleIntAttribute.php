<?php

namespace Sunhill\Tests\TestSupport\Attributes;

use Sunhill\Attributes\BasicAttribute;
use Sunhill\Types\TypeInteger;

class SimpleIntAttribute extends BasicAttribute
{
    public static $attribute_name = 'simpleintattribute';
    
    protected static $value_type = TypeInteger::class;
    
}