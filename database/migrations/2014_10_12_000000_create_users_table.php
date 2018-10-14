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
            $table->string('gender',6);
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->sting('devicetoken')->nullable();
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
            $table->string('genderpreference',6); //options: male, female o both
            $table->unsignedtinyInteger('inagepreference')->default(18);
            $table->unsignedtinyInteger('outagepreference')->default(99);
            $table->unsignedTinyInteger('eventdistance')->default(25);


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
