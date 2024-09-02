<?php
namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_25Filter extends TestFilter
{
    
    static protected $group = 'GroupB';
    
    static protected $priority = 25;
    
    static protected $result = 'SUFFICIENTSTOP';
    
    protected static function initializeConditions()
    {
        static::$conditions = ['condition_25'=>true];
    }
    
    
}