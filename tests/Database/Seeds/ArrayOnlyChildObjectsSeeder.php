<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArrayOnlyChildObjectsSeeder extends Seeder
{
    
    const DATA = [
        ['id'=>20],
        ['id'=>21],
    ];
    
    public function run(): void
    {
        DB::table('arrayonlychildobjects')->insert(ArrayOnlyChildObjectsSeeder::DATA);    
    }
    
}