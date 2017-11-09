<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('FBid');
            $table->string('name');
            $table->string('surnames');
            $table->boolean('gender');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestampsTz();
            $table->timestampTz('last_connection');
            $table->binary('photo');
            $table->date('birthdate');
            $table->string('job', 50)->nullable();
            $table->string('studies', 50)->nullable();
            $table->decimal('ranking', 5, 3)->nullable();
            $table->unsignedBigInteger('aceptar');
            $table->unsignedBigInteger('saludar');
            $table->unsignedBigInteger('rechazar');
            $table->timestampTz('destacado_ini')->nullable();
            $table->timestampTz('destacado_fin')->nullable();
            $table->string('customer')->unique()->nullable();
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
        Schema::dropIfExists('users');
    }
}
