<?php

namespace Sunhill\Tests\Unit\Tests\Examples;

class TestClass
{
    protected $protected_member = 10;
    
    protected function protectedMethod($param)
    {
        $old = $this->protected_member;
        $this->protected_member = $param;
        return $old;
    }
    
    public function getProtectedMember(): int
    {
        return $this->protected_member;
    }
}

