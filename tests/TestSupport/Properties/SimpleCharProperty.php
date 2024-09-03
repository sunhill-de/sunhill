<?php

namespace Sunhill\Properties\Tests\TestSupport\Properties;

use Sunhill\Properties\Properties\AbstractSimpleProperty;

class SimpleCharProperty extends AbstractSimpleProperty
{
    
    public function __construct()
    {
        $this->setName('test_char');
    }
    
    public function isValid($value): bool
    {
        return true;    
    }
    
    public function getAccessType(): string
    {
        return 'string';
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'NonAbstractSimpleProperty');
        static::addInfo('description', 'A base test class for an abstract simple property.', true);
    }
    
}

