<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFullShelvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('full_shelves', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('user_id');
            $table->string('barcode', 20);
            $table->text('title');
            $table->text('callno');
            $table->smallInteger('position')->nullable();
            $table->smallInteger('cposition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('full_shelves');
    }
}
