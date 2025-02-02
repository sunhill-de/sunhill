<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyGrandChildrenSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('dummygrandchildren')->insert([
            ['id'=>15,'dummygrandchildint'=>911],
        ]);    
    }
    
}