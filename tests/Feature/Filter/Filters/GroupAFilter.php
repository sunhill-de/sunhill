<?php

namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupAFilter extends TestFilter
{
    protected static $group = 'GroupA';

    public function execute(): string
    {
        $this->container->setCondition('groupA', 'executed');

        return 'CONTINUE';
    }
}
