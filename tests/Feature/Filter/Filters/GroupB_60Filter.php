<?php
namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_60Filter extends TestFilter
{
    
    static protected $group = 'GroupB';
    
    static protected $priority = 60;
    
    static protected $result = 'CONTINUE';
    
    protected static function initializeConditions()
    {
        static::$conditions = ['condition_60'=>true,'additional'=>'ABC'];
    }
    
    
}