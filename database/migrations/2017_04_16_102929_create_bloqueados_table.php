<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBloqueadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bloqueados', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->UnsignedBigInteger('bloqueado_id');
            $table->UnsignedBigInteger('bloqueador_id');
            $table->string('bloqueador_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bloqueados');
    }
}
