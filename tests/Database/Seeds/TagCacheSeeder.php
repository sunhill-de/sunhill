<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagCacheSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('tagcache')->insert([
            [
                'path_name'=>'TagA',
                'tag_id'=>1,
                'is_fullpath'=>1,
            ],  
            [
                'path_name'=>'TagB',
                'tag_id'=>2,
                'is_fullpath'=>1,
            ],
            [
                'path_name'=>'TagC',
                'tag_id'=>3,
                'is_fullpath'=>0,
            ],
            [
                'path_name'=>'TagB.TagC',
                'tag_id'=>3,
                'is_fullpath'=>1,
            ],
            [
                'path_name'=>'TagD',
                'tag_id'=>4,
                'is_fullpath'=>1,
            ],
            [
                'path_name'=>'TagA',
                'tag_id'=>5,
                'is_fullpath'=>0,
            ],
            [
                'path_name'=>'TagD.TagA',
                'tag_id'=>5,
                'is_fullpath'=>1,
            ],
            [
                'path_name'=>'TagE',
                'tag_id'=>6,
                'is_fullpath'=>1,
            ],
            [
                'path_name'=>'TagF',
                'tag_id'=>7,
                'is_fullpath'=>0,
            ],
            [
                'path_name'=>'TagE.TagF',
                'tag_id'=>7,
                'is_fullpath'=>1,
            ],
            [
                'path_name'=>'TagG',
                'tag_id'=>8,
                'is_fullpath'=>0,
            ],
            [
                'path_name'=>'TagF.TagG',
                'tag_id'=>8,
                'is_fullpath'=>0,
            ],
            [
                'path_name'=>'TagE.TagF.TagG',
                'tag_id'=>8,
                'is_fullpath'=>1,
            ],
        ]);    
    }
    
}