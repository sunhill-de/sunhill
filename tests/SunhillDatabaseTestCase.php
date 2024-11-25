<?php

namespace Sunhill\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithEnv;
use Sunhill\Tests\Database\Seeds\ObjectsSeeder;
use Sunhill\Tests\Database\Seeds\DummiesSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
 
    /**
     * Checks if the database has the given table
     * 
     * @param unknown $table
     */
    public function assertDatabaseHasTable(string $table)
    {
        $this->assertTrue(DBTableExists($table));    
    }
    
    /**
     * Checks if the database has the given table
     *
     * @param unknown $table
     */
    public function assertDatabaseHasNotTable(string $table)
    {
        $this->assertFalse(DBTableExists($table));
    }
    
    /**
     * Checks if the table has the given column
     * 
     * @param unknown $table
     * @param unknown $column
     */
    public function assertDatabaseTableHasColumn($table, $column)
    {
        $this->assertTrue(DBTableHasColumn($table, $column));
    }
    
    public function assertDatabaseTableHasNotColumn($table, $column)
    {
        $this->assertFalse(DBTableHasColumn($table, $column));
    }
    
    public function assertDatabaseTableColumnIsType($table, $column, $type)
    {
        $this->assertEquals($type, DBTableColumnType($table, $column));
    }
}
