<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesSeeder extends Seeder
{
    
    const DATA = [
        ['id'=>1,'name'=>'str_attribute','type'=>'string'],
        ['id'=>2,'name'=>'int_attribute','type'=>'integer'],
        ['id'=>3,'name'=>'float_attribute','type'=>'float'],
        ['id'=>4,'name'=>'another_string','type'=>'string'],
    ];
    
    public function run(): void
    {
        DB::table('attributes')->insert(AttributesSeeder::DATA);    
    }
    
}