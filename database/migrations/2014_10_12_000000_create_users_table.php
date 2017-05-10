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
            $table->string('name');
            $table->string('surnames');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestampsTz();
            $table->timestampTz('last_connection');
            $table->binary('photo');
            $table->date('birthdate');
            $table->string('job', 50);
            $table->string('studies', 50);
            $table->decimal('ranking', 5, 3);
            $table->unsignedBigInteger('aceptar');
            $table->unsignedBigInteger('saludar');
            $table->unsignedBigInteger('rechazar');
            $table->timestampTz('destacado_ini');
            $table->timestampTz('destacado_fin');

        });

        DB::statement('ALTER TABLE users ADD location POINT' );
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
