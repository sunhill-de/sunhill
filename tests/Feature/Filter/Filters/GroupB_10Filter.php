<?php

namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_10Filter extends TestFilter
{
    
    static protected $group = 'GroupB';
    
    static protected $priority = 10;
    
    static protected $result = 'CONTINUE';
  
    protected static function initializeConditions()
    {
        static::$conditions = ['condition_10'=>true];
    }
    
    
}