<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyChildrenSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('dummychildren')->insert([
            ['id'=>13,'dummychildint'=>919],
            ['id'=>15,'dummychildint'=>979],
        ]);    
    }
    
}