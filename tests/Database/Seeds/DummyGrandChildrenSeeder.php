<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyGrandChildrenSeeder extends Seeder
{
    
    const DATA = [
        ['id'=>15,'dummygrandchildint'=>911],
    ];
    
    public function run(): void
    {
        DB::table('dummygrandchildren')->insert(DummyGrandChildrenSeeder::DATA);    
    }
    
}