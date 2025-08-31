<?php

namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_30Filter extends TestFilter
{
    protected static $group = 'GroupB';

    protected static $priority = 30;

    protected static $result = 'SUFFICIENT';

    protected static function initializeConditions()
    {
        static::$conditions = ['condition_30' => true];
    }
}
