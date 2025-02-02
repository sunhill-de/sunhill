<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArrayOnlyChildObjectsSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('arrayonlychildobjects')->insert([
            ['id'=>20],
            ['id'=>21],
        ]);    
    }
    
}