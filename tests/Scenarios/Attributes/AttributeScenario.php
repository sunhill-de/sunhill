<?php
namespace Sunhill\Tests\Scenarios\Attributes;

use Sunhill\Tests\Scenarios\BasicScenario;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class AttributeScenario extends BasicScenario
{
    
    protected $truncate = [];
    
    public function migrate()
    {
        Schema::dropIfExists('attr_simpleintattributes');
        Schema::create('attr_simpleintattributes', function (Blueprint $table) 
        {
            $table->integer('id')->primary();
            $table->integer('value');
            
            $table->primary('id');
        });
        Schema::dropIfExists('attr_simplestringattributes');
        Schema::create('attr_simplestringattributes', function (Blueprint $table)
        {
            $table->integer('id')->primary();
            $table->string('value',10);
            
            $table->primary('id');
        });
    }
    
    public function seed()
    {
        DB::table('attr_simpleintattributes')->insert([
            [
                'id'=>1,
                'value'=>777,
            ],
            [
                'id'=>2,
                'value'=>222,
            ],
            [
                'id'=>3,
                'value'=>999,
            ],
            [
                'id'=>4,
                'value'=>222,
            ],
            [
                'id'=>5,
                'value'=>111,
            ],
        ]);    
        DB::table('attr_simplestringattributes')->insert([
            [
                'id'=>1,
                'value'=>'aBc',
            ],
            [
                'id'=>2,
                'value'=>'dEf',
            ],
            [
                'id'=>3,
                'value'=>'gHi',
            ],
            [
                'id'=>4,
                'value'=>'jKl',
            ],
            [
                'id'=>5,
                'value'=>'mNo',
            ],
        ]);
        DB::table('attributeobjectassigns')->insert([
            [
                'container_id'=>1,
                'attribute_name'=>'simpleintattributes',
                'attribute_id'=>2,
            ],
            [
                'container_id'=>2,
                'attribute_name'=>'simpleintattributes',
                'attribute_id'=>4,
            ],
            [
                'container_id'=>1,
                'attribute_name'=>'simplestringattributes',
                'attribute_id'=>4,
            ],
            [
                'container_id'=>2,
                'attribute_name'=>'simplestringattributes',
                'attribute_id'=>1,
            ]
        ]);
    }
    
}