<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummiesSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('dummies')->insert([
            ['id'=>1,'dummyint'=>123],  
            ['id'=>2,'dummyint'=>234],
            ['id'=>3,'dummyint'=>345],
            ['id'=>4,'dummyint'=>456],
            ['id'=>5,'dummyint'=>567],
            ['id'=>6,'dummyint'=>678],
            ['id'=>13,'dummyint'=>999],
            ['id'=>14,'dummyint'=>987],
            ['id'=>15,'dummyint'=>986],
            ['id'=>16,'dummyint'=>976],
        ]);    
    }
    
}