<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BigParentsSeeder extends Seeder
{
    
    const DATA = [
        [
            'id'=>22,
            'p_int'=>820,
            'p_string'=>'Pixies',
            'p_float'=>9.228,
            'p_bool'=>true,
            'p'
        ]
        
    ];
    
    public function run(): void
    {
        DB::table('dummies')->insert(DummiesSeeder::DATA);    
    }
    
}