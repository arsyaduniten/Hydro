<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gate_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('gate_id')->unsigned();
            $table->foreign('gate_id')->references('id')->on('gates');
            $table->json('records');
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
        Schema::dropIfExists('gate_records');
    }
}
