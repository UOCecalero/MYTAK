<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->increments('id');
            $table->timestampsTz();
            $table->unsignedBigInteger('creator');
            $table->string('nombre');
            $table->binary('photo');
            $table->timestampTz('event_ini');
            $table->timestampTz('event_fin');
            $table->unsignedMediumInteger('aforo');
            $table->timestampTz('destacado_ini');
            $table->timestampTz('destacado_fin');
            $table->string('location_name');
            $table->float('lat',10,6)->nullable();
            $table->float('lng',10,6)->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eventos');
    }
}
