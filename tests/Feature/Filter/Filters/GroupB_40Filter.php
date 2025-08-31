<?php

namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_40Filter extends TestFilter
{
    protected static $group = 'GroupB';

    protected static $priority = 40;

    protected static $result = 'STOP';

    protected static function initializeConditions()
    {
        static::$conditions = ['condition_40' => true];
    }
}
