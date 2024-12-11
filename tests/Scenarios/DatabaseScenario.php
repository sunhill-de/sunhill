<?php
namespace Sunhill\Tests\Scenarios;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class DatabaseScenario extends BasicScenario
{
    
    protected $truncate = [];
    
    public function migrate()
    {
        Schema::dropIfExists('tableA');
        Schema::create('tableA', function (Blueprint $table) 
        {
            $table->integer('id')->primary();
            $table->integer('value');
            $table->integer('link_to_tableB')->nullable();
            $table->integer('link_to_tableA')->nullable();
            
            $table->primary('id');
        });

        Schema::dropIfExists('tableB');
        Schema::create('tableB', function (Blueprint $table) 
        {
            $table->integer('id')->primary();
            $table->integer('Bvalue')->default(123);
            $table->string('str_value',10)->nullable();
                
            $table->primary('id');
        });
        
        Schema::dropIfExists('tableC');
        Schema::create('tableC', function (Blueprint $table)
        {
            $table->integer('id')->primary();
            $table->integer('Cvalue');
            
            $table->primary('id');
        });
    }
    
    public function seed()
    {
        DB::table('tableA')->insert([
            ['id'=>1,'value'=>400,'link_to_tableA'=>null,'link_to_tableB'=>1],
            ['id'=>2,'value'=>200,'link_to_tableA'=>1,'link_to_tableB'=>2],
            ['id'=>3,'value'=>800,'link_to_tableA'=>1,'link_to_tableB'=>null],
            ['id'=>4,'value'=>100,'link_to_tableA'=>2,'link_to_tableB'=>2],
        ]);
        DB::table('tableB')->insert([
            ['id'=>1,'Bvalue'=>400,'str_value'=>'ABC'],
            ['id'=>2,'BValue'=>123,'str_value'=>'DEF'],
            ['id'=>3,'Bvalue'=>700,'str_value'=>'XYZ'],
            ['id'=>4,'BValue'=>123,'str_value'=>null],
        ]);
        DB::table('tableC')->insert([
            ['id'=>1,'Cvalue'=>123],
            ['id'=>2,'Cvalue'=>345],
            ['id'=>3,'Cvalue'=>234],
            ['id'=>4,'CValue'=>456],
        ]);
    }
    
}