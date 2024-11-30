<?php

namespace Sunhill\Tests\Scenarios\Obejcts;

use Sunhill\Tests\Scenarios\BasicScenario;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class ParentObjectScenario extends BasicScenario
{
    
    public function migrate()
    {
        Schema::dropIfExists('parentobjects');
        Schema::create('parentobjects', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('parent_int');
            $table->string('parent_string');
            
            $table->primary('id');
        });            
        Schema::dropIfExists('parentobjects');
        Schema::create('parentobjects', function (Blueprint $table) {
            $table->integer('container_id');
            $table->integer('index');
            $table->integer('element');
                
            $table->primary(['container_id','index']);
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
        DB::table('parentobjects')->insert([
            ['id'=>7,'parent_int'=>111,'parent_string'=>'AAA'],
            ['id'=>8,'parent_int'=>222,'parent_string'=>'BBB'],
            
            ['id'=>9, 'parent_int'=>333,'parent_string'=>'CCC'],
            ['id'=>10,'parent_int'=>444,'parent_string'=>'DDD'],
            ['id'=>11,'parent_int'=>555,'parent_string'=>'EEE'],
            ['id'=>12,'parent_int'=>666,'parent_string'=>'FFF'],
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
            'parent_int'=>makeStdclass(['name'=>'parent_int','type'=>'integer','storage_subid'=>'parentobjects']),
            'parent_string'=>makeStdclass(['name'=>'parent_string','type'=>'string','max_length'=>3,'storage_subid'=>'parentobjects']),
            'parent_sarray'=>makeStdClass(['name'=>'parent_sarray','type'=>'array','index_type'=>'integer','element_type'=>'integer','storage_subid'=>'parentobjects']),
        ];
    }
    
}