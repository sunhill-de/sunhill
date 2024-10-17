<?php

namespace Sunhill\Tests\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ObjectsSeeder extends Seeder
{
    
    public function run(): void
    {
        DB::table('objects')->insert([
            [
                'id'=>1,
                '_classname'=>'Dummy',
                '_uuid'=>'de4961ab-f548-4402-8adc-f6d33e80134e',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],  
            [
                'id'=>2,
                '_classname'=>'Dummy',
                '_uuid'=>'5a1f9541-4245-4e20-99c6-2229c9b95707',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,                
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>3,
                '_classname'=>'Dummy',
                '_uuid'=>'e7e1dc3f-9db0-42b1-b141-41ae94db9c5c',
                '_read_cap'=>'reader',
                '_modify_cap'=>null,
                '_delete_cap'=>null,                
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>4,
                '_classname'=>'Dummy',
                '_uuid'=>'8807dbef-eb26-41f9-ac89-3744cfb262a0',
                '_read_cap'=>null,
                '_modify_cap'=>'modifier',
                '_delete_cap'=>null,                
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 20:55:00',
            ],
            [
                'id'=>5,
                '_classname'=>'Dummy',
                '_uuid'=>'f9c5cc37-596f-4e21-a3a7-4529fe6e5925',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>'deleter',                
                '_created_at'=>'2024-10-18 13:55:00',
                '_updated_at'=>'2024-10-18 13:55:00',
            ],
            [
                'id'=>6,
                '_classname'=>'Dummy',
                '_uuid'=>'77e7b267-57dc-4223-a326-e7720ed510ff',
                '_read_cap'=>'important',
                '_modify_cap'=>'important',
                '_delete_cap'=>'important',                
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
        ]);    
    }
    
}