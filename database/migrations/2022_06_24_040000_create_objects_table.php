<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObjectsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('_classname');
            $table->string('_uuid',40);
            $table->string('_read_cap',20)->nullable()->default(null);
            $table->string('_modify_cap',20)->nullable()->default(null);;
            $table->string('_delete_cap',20)->nullable()->default(null);;
            $table->datetime('_created_at')->nullable()->default(null);
            $table->datetime('_updated_at')->nullable()->default(null);

            $table->index('_uuid');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('objects');
    }
}
