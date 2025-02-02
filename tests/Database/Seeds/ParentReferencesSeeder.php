<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParentReferencesSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('parentreferences')->insert([
            ['id'=>17,'parent_int'=>1111,'parent_reference'=>1],  
            ['id'=>18,'parent_int'=>2222,'parent_reference'=>null],
            ['id'=>19,'parent_int'=>3333,'parent_reference'=>null],            
       ]);    

    }
    
}