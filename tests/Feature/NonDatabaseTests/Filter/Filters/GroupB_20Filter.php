<?php
/**
 * @file GroupB_20Filter.php
 * Type: Support file for tests
 */

namespace Sunhill\Tests\Feature\NonDatabaseTests\Filter\Filters;

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
