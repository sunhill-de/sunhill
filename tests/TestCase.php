<?php

namespace Sunhill\Tests;

use \Orchestra\Testbench\TestCase as Orchestra;
use Sunhill\SunhillServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            SunhillServiceProvider::class,
        ];        
    }
        //
}
