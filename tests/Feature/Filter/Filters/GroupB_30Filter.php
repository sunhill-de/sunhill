<?php

namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_30Filter extends TestFilter
{
    
    static protected $group = 'GroupB';
    
    static protected $priority = 30;
    
    static protected $result = 'SUFFICIENT';

    protected static function initializeConditions()
    {
        static::$conditions = ['condition_30'=>true];
    }
    
    
}