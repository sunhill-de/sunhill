<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentObjects_parent_sarraySeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('parentobjects_parent_sarray')->insert([
            ['container_id'=>7,'index'=>0,'element'=>10],
            ['container_id'=>7,'index'=>1,'element'=>11],
            ['container_id'=>7,'index'=>2,'element'=>12],

            ['container_id'=>9,'index'=>0,'element'=>30],
            ['container_id'=>9,'index'=>1,'element'=>31],
            ['container_id'=>9,'index'=>2,'element'=>32],
            
            ['container_id'=>10,'index'=>0,'element'=>40],
            ['container_id'=>10,'index'=>1,'element'=>41],
            ['container_id'=>10,'index'=>2,'element'=>42],
        ]);    
    }
    
}