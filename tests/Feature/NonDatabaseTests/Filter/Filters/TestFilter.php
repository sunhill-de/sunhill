<?php
/**
 * @file TestFilter.php
 * Type: Support file for tests
 */
namespace Sunhill\Tests\Feature\NonDatabaseTests\Filter\Filters;

use Sunhill\Filter\Filter;

class TestFilter extends Filter
{
    protected static $group = 'GroupB';

    protected static $priority = 10;

    protected static $result = 'CONTINUE';

    public function execute(): string
    {
        $this->container->setCondition('groupB', $this->container->getCondition('groupB').'_'.static::$priority);

        return static::$result;
    }
}
