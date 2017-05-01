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
            $table->string('nombre', 50);
            $table->binary('photo');
            $table->timestampTz('event_ini');
            $table->timestampTz('event_fin');
            $table->decimal('price', 5, 2);
            $table->unsignedMediumInteger('aforo');
            $table->timestampTz('destacado_ini');
            $table->timestampTz('destacado_fin');

        });

        DB::statement('ALTER TABLE eventos ADD location POINT');
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
