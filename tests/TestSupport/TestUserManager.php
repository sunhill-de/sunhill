<?php

namespace Sunhill\Tests\TestSupport;

class TestUserManager
{
    
    static public function hasCapability(string $key): bool
    {
        if ($key == 'required'){
            return true;
        }
        return false;
    }
    
}