<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParentReferencesTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parentreferences', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('parent_int');
            $table->integer('parent_reference')->nullable()->default(null);
            
            $table->primary('id');
        });
    }
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parentreferences');
    }
}
