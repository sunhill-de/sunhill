<?php

namespace Sunhill\Tests\Scenarios\Obejcts;

use Sunhill\Tests\Scenarios\BasicScenario;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class DummyScenario extends BasicScenario
{
    
    protected $truncate = ['objects','dummies'];
    
    public function migrate()
    {
        Schema::dropIfExists('dummies');
        Schema::create('dummies', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('dummyint');
            
            $table->primary('id');
        });            
    }
    
    public function seed()
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
        DB::table('dummies')->insert([
            ['id'=>1,'dummyint'=>123],
            ['id'=>2,'dummyint'=>234],
            ['id'=>3,'dummyint'=>345],
            ['id'=>4,'dummyint'=>456],
            ['id'=>5,'dummyint'=>567],
            ['id'=>6,'dummyint'=>678],
        ]);
    }
    
    public function structure()
    {
        return [
            '_uuid'=>makeStdclass(['name'=>'_uuid','type'=>'string','max_length'=>40,'storage_subid'=>'objects']),
            '_read_cap'=>makeStdclass(['name'=>'_read_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
            '_modify_cap'=>makeStdclass(['name'=>'_modify_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
            '_delete_cap'=>makeStdclass(['name'=>'_delete_cap','type'=>'string','max_length'=>20,'storage_subid'=>'objects']),
            '_created_at'=>makeStdclass(['name'=>'_created_at','type'=>'datetime','storage_subid'=>'objects']),
            '_modified_at'=>makeStdclass(['name'=>'_modified_at','type'=>'datetime','storage_subid'=>'objects']),
            'dummyint'=>makeStdclass(['name'=>'dummyint','type'=>'integer','storage_subid'=>'dummies']),
        ];        
    }
    
}