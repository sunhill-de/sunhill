<?php

namespace Sunhill\Tests\Unit\Checker\Examples;

use Sunhill\Checker\Checker;

class AnotherDummyChecker extends Checker
{
    
    public function checkSomething(bool $repair)
    {
        $this->pass();
    }
    
}
