<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagObjectAssignsSeeder extends Seeder
{
    
    const DATA = [
        [
            'container_id'=>1,
            'tag_id'=>1,
        ],
        [
            'container_id'=>2,
            'tag_id'=>1,
        ],
        [
            'container_id'=>2,
            'tag_id'=>3,
        ],
        [
            'container_id'=>3,
            'tag_id'=>3,
        ],
        [
            'container_id'=>3,
            'tag_id'=>8,
        ],
    ];
    
    public function run(): void
    {
        DB::table('tagobjectassigns')->insert(static::DATA);    
    }
    
}