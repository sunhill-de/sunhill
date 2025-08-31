<?php

namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_60Filter extends TestFilter
{
    protected static $group = 'GroupB';

    protected static $priority = 60;

    protected static $result = 'CONTINUE';

    protected static function initializeConditions()
    {
        static::$conditions = ['condition_60' => true, 'additional' => 'ABC'];
    }
}
