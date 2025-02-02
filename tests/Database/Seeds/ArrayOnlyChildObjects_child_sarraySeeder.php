<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArrayOnlyChildObjects_child_sarraySeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('arrayonlychildobjects_child_sarray')->insert([
            ['container_id'=>20,'index'=>0,'element'=>2000],
            ['container_id'=>20,'index'=>1,'element'=>2100],
            ['container_id'=>20,'index'=>2,'element'=>2200],
        ]);    
    }
    
}