<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChildObjectsSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('childobjects')->insert([
            ['id'=>9, 'child_int'=>212,'child_string'=>'BCD'],
            ['id'=>10,'child_int'=>222,'child_string'=>'CDE'],
            ['id'=>11,'child_int'=>232,'child_string'=>'DEF'],
            ['id'=>12,'child_int'=>242,'child_string'=>'EFG'],
        ]);    
    }
    
}