<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBloqueadorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bloqueadors', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->UnsignedBigInteger('user_id');
            $table->UnsignedBigInteger('bloqueador_id');
            $table->string('bloqueador_type'); //user o empresa
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bloqueadors');
    }
}
