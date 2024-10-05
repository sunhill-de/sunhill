<?php

namespace Sunhill\Tests\Feature\Filter\Filters;


class GroupB_50Filter extends TestFilter
{
    
    static protected $group = 'GroupB';
    
    static protected $priority = 50;
    
    static protected $result = 'CONTINUE';

    protected static function initializeConditions()
    {
        static::$conditions = ['condition_50'=>true];
    }
    
    
}