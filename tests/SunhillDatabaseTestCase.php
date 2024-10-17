<?php

namespace Sunhill\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithEnv;
use Sunhill\Tests\Database\Seeds\ObjectsSeeder;
use Sunhill\Tests\Database\Seeds\DummiesSeeder;
use Illuminate\Support\Facades\DB;

class SunhillDatabaseTestCase extends TestCase
{

    use RefreshDatabase;
    
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(dirname(__FILE__).'/Database/Migrations');
        $this->artisan('migrate', ['--database'=>'testbench'])->run();
    }
    
    protected function getEnvironmentSetUp($app)
    {
        # Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
    
}
