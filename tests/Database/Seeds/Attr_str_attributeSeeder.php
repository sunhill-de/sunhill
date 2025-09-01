<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Attr_str_attributeSeeder extends Seeder
{
    
    const DATA = [
        ['container_id'=>1,'value'=>'attribute'],
        ['container_id'=>2,'value'=>'some_value'],
        ['container_id'=>3,'value'=>'another_value'],
        ['container_id'=>20,'value'=>'some_value'],
    ];
    
    public function run(): void
    {
        DB::table('attr_str_attribute')->insert(Attr_str_attributeSeeder::DATA);    
    }
    
}