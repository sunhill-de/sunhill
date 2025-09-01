<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChildObjectsChildSArrayTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('childobjects_child_sarray', function (Blueprint $table) {
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
        Schema::dropIfExists('childobjects_child_sarray');
    }
}
