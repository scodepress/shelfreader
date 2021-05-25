<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFullMovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('full_moves', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->string('barcode', 20);
            $table->integer('moved')->default(0);
            $table->integer('position');
            $table->integer('cposition');
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
        Schema::dropIfExists('full_moves');
    }
}
