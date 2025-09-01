<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParentReferencesParentRArrayTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parentreferences_parent_rarray', function (Blueprint $table) {
            $table->integer('container_id');
            $table->integer('index');
            $table->integer('element');
            
            $table->primary(['container_id','index']);
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parentreferences_parent_rarray');
    }
}
