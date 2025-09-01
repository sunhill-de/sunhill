<?php
/**
 * @file GroupAFilter.php
 * Type: Support file for tests
 */

namespace Sunhill\Tests\Feature\NonDatabaseTests\Filter\Filters;

class GroupAFilter extends TestFilter
{
    protected static $group = 'GroupA';

    public function execute(): string
    {
        $this->container->setCondition('groupA', 'executed');

        return 'CONTINUE';
    }
}
