<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasterKeysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_keys', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('position');
            $table->bigInteger('user_id');
            $table->bigInteger('library_id');
            $table->text('title');
            $table->string('barcode', 20);
            $table->string('callno', 100);
            $table->string('prefix', 3);
            $table->integer('tp1');
            $table->decimal('tp2', 15, 15);
            $table->string('pre_date', 5);
            $table->integer('pvn')->nullable();
            $table->string('pvl', 10)->nullable();
            $table->string('cutter', 3);
            $table->decimal('pcd', 15, 15);
            $table->integer('cutter_date')->nullable();
            $table->string('inline_cutter', 3);
            $table->decimal('inline_cutter_decimal', 15, 15);
            $table->integer('cutter_date2')->nullable();
            $table->string('cutter2', 3);
            $table->decimal('pcd2', 15, 15);
            $table->string('part1', 250);
            $table->string('part2', 20);
            $table->string('part3', 20);
            $table->string('part4', 20);
            $table->string('part5', 20);
            $table->string('part6', 20);
            $table->string('part7', 20)->default('0');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('master_keys');
    }
}
