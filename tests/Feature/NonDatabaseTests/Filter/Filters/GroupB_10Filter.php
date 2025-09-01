<?php
/**
 * @file GroupB_10Filter.php
 * Type: Support file for tests
 */

namespace Sunhill\Tests\Feature\NonDatabaseTests\Filter\Filters;

class GroupB_10Filter extends TestFilter
{
    protected static $group = 'GroupB';

    protected static $priority = 10;

    protected static $result = 'CONTINUE';

    protected static function initializeConditions()
    {
        static::$conditions = ['condition_10' => true];
    }
}
