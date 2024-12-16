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
            $table->string('attribute_name');
            $table->integer('attribute_id');
            
            $table->primary(['container_id','attribute_name','attribute_id']);
            
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
