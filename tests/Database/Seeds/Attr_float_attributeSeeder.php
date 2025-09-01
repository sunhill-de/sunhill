<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Attr_float_attributeSeeder extends Seeder
{
    
    const DATA = [
        ['container_id'=>1,'value'=>1.23],
    ];
    
    public function run(): void
    {
        DB::table('attr_float_attribute')->insert(Attr_float_attributeSeeder::DATA);    
    }
    
}