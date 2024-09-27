<?php

namespace Sunhill\Tests;

use \Orchestra\Testbench\TestCase as Orchestra;
use Sunhill\SunhillServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;

class TestCase extends Orchestra
{
    use WithWorkbench;
    
    protected function getPackageProviders($app)
    {
        return [
            SunhillServiceProvider::class,
        ];        
    }
        //
}
