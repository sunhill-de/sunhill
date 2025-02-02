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
            [
                'id'=>7, // A parent object with array
                '_classname'=>'ParentObject',
                '_uuid'=>'73f4bebe-bb01-4812-abb0-69b9b3b132ea',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>8,// A parent object with empty array
                '_classname'=>'ParentObject',
                '_uuid'=>'8819d610-1b64-4db0-8aaf-dee7992f5cef',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>9, // A child object with both arrays
                '_classname'=>'ChildObject',
                '_uuid'=>'428d7089-1f7c-434d-b940-9976d5afbc04',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>10, // A child object with only parent array
                '_classname'=>'ParentObject',
                '_uuid'=>'4382f710-286c-4577-baf5-b5ecb08cd99c',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>11,// A child object with only child array
                '_classname'=>'ParentObject',
                '_uuid'=>'b92f34f4-5f0a-4941-b359-b4fc21c3ddb6',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>12,// A child object with empty arrays
                '_classname'=>'ParentObject',
                '_uuid'=>'3ae0ebe5-7165-4512-aa38-8ad5da515e04',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>13,// A DummyChild
                '_classname'=>'DummyChild',
                '_uuid'=>'ca4dbd25-e054-4650-91f3-c5923b0b86fb',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>14,// A SkippingDummyChild
                '_classname'=>'SkippingDummyChild',
                '_uuid'=>'5931b225-5c4a-4a35-8c38-7a09be20512c',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>15,// A DummyGrandChild
                '_classname'=>'DummyGrandChild',
                '_uuid'=>'e7f7330a-923b-4e5c-ab45-802e029477cb',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
            [
                'id'=>16,// A SkippingDummyGrandChild
                '_classname'=>'SkippingDummyGrandChild',
                '_uuid'=>'3962cb40-1f33-4d05-b0e0-2bc1ade2f467',
                '_read_cap'=>null,
                '_modify_cap'=>null,
                '_delete_cap'=>null,
                '_created_at'=>'2024-10-17 13:55:00',
                '_updated_at'=>'2024-10-17 13:55:00',
            ],
        ]);    
    }
    
}