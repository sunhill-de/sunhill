<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentObjectsSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('parentobjects')->insert([
            ['id'=>7,'parent_int'=>111,'parent_string'=>'AAA'],  
            ['id'=>8,'parent_int'=>222,'parent_string'=>'BBB'],
    
            ['id'=>9, 'parent_int'=>333,'parent_string'=>'CCC'],
            ['id'=>10,'parent_int'=>444,'parent_string'=>'DDD'],
            ['id'=>11,'parent_int'=>555,'parent_string'=>'EEE'],
            ['id'=>12,'parent_int'=>666,'parent_string'=>'FFF'],

            ['id'=>20,'parent_int'=>5555,'parent_string'=>'ERE'],
            ['id'=>21,'parent_int'=>6666,'parent_string'=>'FRF'],
            
        ]);    

    }
    
}