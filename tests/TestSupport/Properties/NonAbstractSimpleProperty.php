<?php

namespace Sunhill\Properties\Tests\TestSupport\Properties;

use Sunhill\Properties\Properties\AbstractSimpleProperty;

class NonAbstractSimpleProperty extends AbstractSimpleProperty
{
    
    public function __construct()
    {
        $this->setName('test_int2');
    }
    
    public function isValid($value): bool
    {
        return is_int($value);    
    }
    
    public function getAccessType(): string
    {
        return 'integer';
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'NonAbstractSimpleProperty');
        static::addInfo('description', 'A base test class for an abstract simple property.', true);
    }
    
}

