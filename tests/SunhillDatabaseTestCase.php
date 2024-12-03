<?php

namespace Sunhill\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithEnv;
use Sunhill\Tests\Database\Seeds\ObjectsSeeder;
use Sunhill\Tests\Database\Seeds\DummiesSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SunhillDatabaseTestCase extends SunhillKeepingDatabaseTestCase
{

    use RefreshDatabase;
    
}
