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
        Schema::dropIfExists('simpleintattributes');
        Schema::create('simpleintattributes', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('value');
            
            $table->primary('id');
        });
    }
    
    public function seed()
    {
        DB::table('simpleintattributes')->insert([
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
    }
    
}