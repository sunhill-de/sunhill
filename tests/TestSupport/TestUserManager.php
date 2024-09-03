<?php

namespace Sunhill\Properties\Tests\TestSupport;

class TestUserManager
{
    
    public static function hasCapability(string $capability): bool
    {
        return ($capability == 'required');
    }
    
}