<?php

namespace Sunhill\Tests\TestSupport\Properties;

use Sunhill\Properties\AbstractProperty;
use Sunhill\Storage\AbstractStorage;

class NonAbstractProperty extends AbstractProperty
{
    
    public $is_valid = true;
    
    public $public_storage;
    
    public $expected_storage = AbstractStorage::class;
    
    public function __construct()
    {
        $this->setName('test_int');
    }
    
    public static function getAccessType(): string
    {
        return 'integer';
    }
    
    public static function setAllowedRelations(array $allowed_relations)
    {
        static::$allowed_relations = $allowed_relations;    
    }
    
    protected function isValidStorage(AbstractStorage $storage): bool
    {
        return is_a($storage, $this->expected_storage);    
    }
    
    protected function createStorage(): ?AbstractStorage
    {
        return $this->public_storage;
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

