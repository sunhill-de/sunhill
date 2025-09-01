<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Attr_int_attributeSeeder extends Seeder
{
    
    const DATA = [
        ['container_id'=>1,'value'=>100],
        ['container_id'=>3,'value'=>200],
        ['container_id'=>4,'value'=>300],
    ];
    
    public function run(): void
    {
        DB::table('attr_int_attribute')->insert(Attr_int_attributeSeeder::DATA);    
    }
    
}