<?php

namespace Sunhill\Tests\Unit\Basic\Examples;

use Sunhill\Basic\Base;

class Extension extends Base
{
    
    private $test=0;
    
    public function setTest($value) {
        $this->test = $value;
    }
    
    public function getTest() {
        return $this->test;
    }
    
    protected function ownMethod()
    {
        
    }
    
    protected static function ownStaticMethod()
    {
        
    }
    
}

