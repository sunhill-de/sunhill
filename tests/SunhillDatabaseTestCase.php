<?php

/**
 * @file SunhillDatabaseTestCase
 * A testcase for all kind of tests that need the laravel framework and database
 */

namespace Sunhill\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Sunhill\SunhillServiceProvider;

/**
 * A basic class for simple tests that depend on the laravel framework but not on database access
 *
 * @author klaus
 */
class SunhillDatabaseTestCase extends Orchestra
{
    use WithWorkbench;

    protected function getPackageProviders($app)
    {
        return [
            SunhillServiceProvider::class,
        ];
    }
}
