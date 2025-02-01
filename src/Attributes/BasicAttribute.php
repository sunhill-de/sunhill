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

use Sunhill\Properties\PooledRecordProperty;
use Sunhill\Properties\ElementBuilder;
use Sunhill\Properties\AbstractProperty;
use Sunhill\Storage\AbstractStorage;
use Sunhill\Storage\MysqlStorage\MysqlAttributeStorage;

class BasicAttribute extends PooledRecordProperty
{
  
    protected static $inherited_inclusion = 'none';
    
    public static $attribute_name = '';
  
    protected static $value_type = AbstractProperty::class;

    protected static function getValueType(): string
    {
        return static::$value_type;    
    }
    
    protected static function initializeRecord(ElementBuilder $builder)
    {
        $type = static::getValueType();
        $builder->addProperty(static::getValueType(),'value');
    }
     
    protected function createStorage(): ?AbstractStorage
    {
        $storage = new MysqlAttributeStorage();
        return $storage;
    }
        
}