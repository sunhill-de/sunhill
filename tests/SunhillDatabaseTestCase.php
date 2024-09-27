<?php

namespace Sunhill\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithEnv;

class SunhillDatabaseTestCase extends TestCase
{

    use RefreshDatabase;
    /*
    protected function defineDatabaseMigrations()
    {
        $this->artisan('migrate', ['--database'=>'testbench'])->run();
        
        $this->beforeApplicationDestroyed(
            fn() => $this->artisan($this, 'migrate:rollback', ['--database'=>'testbench'])
            );
    }
    */
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
