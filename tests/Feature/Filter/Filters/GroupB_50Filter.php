<?php

namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_50Filter extends TestFilter
{
    protected static $group = 'GroupB';

    protected static $priority = 50;

    protected static $result = 'CONTINUE';

    protected static function initializeConditions()
    {
        static::$conditions = ['condition_50' => true];
    }
}
