<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkippingDummyGrandChildrenSeeder extends Seeder
{
    
    const DATA = [
        ['id'=>16,'dummygrandchildint'=>9111],
    ];
    
    public function run(): void
    {
        DB::table('skippingdummygrandchildren')->insert(static::DATA);    
    }
    
}