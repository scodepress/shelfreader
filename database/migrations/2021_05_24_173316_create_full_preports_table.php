<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFullPreportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('full_preports', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->string('barcode', 20);
            $table->text('title');
            $table->string('callnum')->nullable();
            $table->string('location_id', 20);
            $table->date('date')->nullable();
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
        Schema::dropIfExists('full_preports');
    }
}
