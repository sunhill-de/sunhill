<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttributeobjectassignsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attributeobjectassigns', function (Blueprint $table)
        {
            $table->integer('container_id');
            $table->integer('attribute_id');
            $table->integer('attribute_index');
            
            $table->primary(['container_id','attribute_id','attribute_index']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attributeobjectassigns');
    }
}
