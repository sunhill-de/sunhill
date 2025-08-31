<?php

namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_20Filter extends TestFilter
{
    protected static $group = 'GroupB';

    protected static $priority = 20;

    protected static $result = 'STOP';

    protected static function initializeConditions()
    {
        static::$conditions = ['condition_20' => true];
    }
}
