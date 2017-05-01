<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerfilsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perfils', function (Blueprint $table) {
            $table->increments('id');
            $table->timestampsTz();
            $table->timestampTz('last_connection');
            $table->string('name', 50);
            $table->string('surnames', 50);
            $table->binary('photo');
            $table->date('birthdate');
            $table->string('job', 50);
            $table->string('studies', 50);
            $table->string('email', 50);
            $table->decimal('ranking', 5, 3);
            $table->unsignedBigInteger('aceptar');
            $table->unsignedBigInteger('saludar');
            $table->unsignedBigInteger('rechazar');
            $table->timestampTz('destacado_ini');
            $table->timestampTz('destacado_fin');


        });

        DB::statement('ALTER TABLE perfils ADD location POINT' );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perfils');
    }
}
