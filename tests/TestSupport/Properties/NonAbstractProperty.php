<?php

namespace Sunhill\Properties\Tests\TestSupport\Properties;

use Sunhill\Properties\Properties\AbstractProperty;

class NonAbstractProperty extends AbstractProperty
{
    
    public $is_valid = true;
    
    public function __construct()
    {
        $this->setName('test_int');
    }
    
    public function getAccessType(): string
    {
        return 'integer';
    }
    
    protected function formatForHuman($input)
    {
        return "A".$input;
    }
    
    public function isValid($input): bool
    {
        return $this->is_valid;
    }
    
    protected function formatFromInput($input)
    {
        return 'Input'.$input;
    }
    
    protected static function setupInfos()
    {
        static::addInfo('name', 'NonAbstractProperty');
        static::addInfo('description', 'A base test class for an abstract property.', true);
        static::addInfo('userkey', 'uservalue');
    }
    
    protected static function translate(string $info): string
    {
        return 'trans:'.$info;
    }
    
}

