<?php

namespace Sunhill\Tests\Feature\Filter\Filters;

class GroupAFilter extends TestFilter
{
    
    static protected $group = 'GroupA';
 
    public function execute(): string
    {
        $this->container->setCondition('groupA','executed');
        return 'CONTINUE';
    }
    
}