<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChildObjects_child_sarraySeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('childobjects_child_sarray')->insert([
            ['container_id'=>9,'index'=>0,'element'=>200],
            ['container_id'=>9,'index'=>1,'element'=>210],
            ['container_id'=>9,'index'=>2,'element'=>220],

            ['container_id'=>11,'index'=>0,'element'=>400],
            ['container_id'=>11,'index'=>1,'element'=>410],
            ['container_id'=>11,'index'=>2,'element'=>420],
            
        ]);    
    }
    
}