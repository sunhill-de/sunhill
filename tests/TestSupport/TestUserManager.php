<?php

namespace Sunhill\Tests\TestSupport;

class TestUserManager
{
    public static function hasCapability(string $key): bool
    {
        if ($key == 'required') {
            return true;
        }

        return false;
    }
}
