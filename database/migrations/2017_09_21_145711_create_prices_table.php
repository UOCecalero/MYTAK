<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
            $table->string('description');
            $table->unsignedBigInteger('evento_id');
            $table->unsignedMediumInteger('precio'); // 2,50 se escribe 250
            $table->BigInteger('availables')->defalut(-1); //Si el valor es -1, no hay límite de este tipo de ticket. Si el valor es 0, este tipo no esta disponible. Si es un entero positivo, es el número de tickets disponibles de este tipo. Hay que tener en cuenta este campo en relacion con el aforo y el decremento cada vez que se compra un ticket.
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prices');
    }
}
