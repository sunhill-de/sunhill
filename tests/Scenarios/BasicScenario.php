<?php

namespace Sunhill\Tests\Scenarios;

use Illuminate\Support\Facades\DB;

class BasicScenario
{
    
    protected $test;
    
    protected $truncate = [];
    
    public function __construct($test)
    {
        $this->test = $test;
        $this->truncate($this->truncate);
    }
    
    public function truncate(array $tables)
    {
        foreach ($tables as $table) {
            DB::truncate($table);
        }
    }
    
    public function migrate()
    {        
        
    }
    
    public function seed()
    {
        
    }
    
    public function structure()
    {
        
    }
    
}