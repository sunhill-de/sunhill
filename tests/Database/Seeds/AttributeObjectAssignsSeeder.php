<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeObjectAssignsSeeder extends Seeder
{
    
    const DATA = [
        ['container_id'=>1,'attribute_id'=>1],
        ['container_id'=>2,'attribute_id'=>1],
        ['container_id'=>3,'attribute_id'=>1],
        ['container_id'=>20,'attribute_id'=>1],
        ['container_id'=>1,'attribute_id'=>2],
        ['container_id'=>3,'attribute_id'=>2],
        ['container_id'=>4,'attribute_id'=>2],
        ['container_id'=>1,'attribute_id'=>3],
    ];
    
    public function run(): void
    {
        DB::table('attributeobjectassigns')->insert(AttributeObjectAssignsSeeder::DATA);    
    }
    
}