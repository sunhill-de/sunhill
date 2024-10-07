<?php

namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_40Filter extends TestFilter
{
    
    static protected $group = 'GroupB';
    
    static protected $priority = 40;
    
    static protected $result = 'STOP';
    
    protected static function initializeConditions()
    {
        static::$conditions = ['condition_40'=>true];
    }
    
    
}