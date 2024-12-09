<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagsSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('tags')->insert([
            [
                'id'=>1,
                'name'=>'TagA',
                'options'=>0,
                'parent_id'=>null,
            ],  
            [
                'id'=>2,
                'name'=>'TagB',
                'options'=>0,
                'parent_id'=>null,
            ],
            [
                'id'=>3,
                'name'=>'TagC',
                'options'=>2,
                'parent_id'=>null,
            ],
            [
                'id'=>4,
                'name'=>'TagD',
                'options'=>0,
                'parent_id'=>null,
            ],
            [
                'id'=>5,
                'name'=>'TagA',
                'options'=>0,
                'parent_id'=>4,
            ],
            [
                'id'=>6,
                'name'=>'TagE',
                'options'=>0,
                'parent_id'=>null,
            ],
            [
                'id'=>7,
                'name'=>'TagF',
                'options'=>0,
                'parent_id'=>6,
            ],
            [
                'id'=>8,
                'name'=>'TagG',
                'options'=>0,
                'parent_id'=>7,
            ],
        ]);    
    }
    
}