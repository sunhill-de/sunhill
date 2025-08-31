<?php

namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupB_25Filter extends TestFilter
{
    protected static $group = 'GroupB';

    protected static $priority = 25;

    protected static $result = 'SUFFICIENTSTOP';

    protected static function initializeConditions()
    {
        static::$conditions = ['condition_25' => true];
    }
}
