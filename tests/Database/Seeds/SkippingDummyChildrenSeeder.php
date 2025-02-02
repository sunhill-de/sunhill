<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkippingDummyChildrenSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('skippingdummychildren')->insert([
            ['id'=>14],
            ['id'=>16],
        ]);    
    }
    
}