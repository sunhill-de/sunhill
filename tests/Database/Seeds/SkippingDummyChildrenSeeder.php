<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkippingDummyChildrenSeeder extends Seeder
{
    
    const DATA = [
        ['id'=>14],
        ['id'=>16],
    ];
    
    public function run(): void
    {
        DB::table('skippingdummychildren')->insert(SkippingDummyChildrenSeeder::DATA);    
    }
    
}