<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentReferences_parent_rarraySeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('parentreferences_parent_rarray')->insert([
            ['container_id'=>17,'index'=>0,'element'=>2],
            ['container_id'=>17,'index'=>1,'element'=>3],
            ['container_id'=>17,'index'=>2,'element'=>4],

            ['container_id'=>18,'index'=>0,'element'=>3],
        ]);    
    }
    
}