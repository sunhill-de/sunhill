<?php
namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_20Filter extends TestFilter
{
    
    static protected $group = 'GroupB';
    
    static protected $priority = 20;
    
    static protected $result = 'STOP';
    
    protected static function initializeConditions()
    {
        static::$conditions = ['condition_20'=>true];
    }
    
    
}