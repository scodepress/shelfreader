<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_statuses', function (Blueprint $table) {
            $table->integer('id');
            $table->integer('user_id');
            $table->string('barcode', 20);
            $table->string('home_location', 20)->nullable();
            $table->string('current_location', 20)->nullable();
            $table->string('due_date', 20)->nullable();
            $table->string('recall_duedate', 20)->nullable();
            $table->string('source_library', 20)->nullable();
            $table->string('destination_library', 20)->nullable();
            $table->string('transit_reason', 20)->nullable();
            $table->string('transit_date', 20)->nullable();
            $table->string('chargeable', 20)->nullable();
            $table->string('number_holds', 20)->nullable();
            $table->string('item_type', 20)->nullable();
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
        Schema::dropIfExists('item_statuses');
    }
}
