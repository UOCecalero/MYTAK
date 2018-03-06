<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tikets', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedBigInteger('random');
            //$table->unsignedBigInteger('evento_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('price_id');
            //$table->string('card_token'); // Si se decide guardar los tokens de la tarjeta hay que modificar PurchasesController@store
            $table->binary('qr');
            $table->string('hash');
            $table->unsignedTinyInteger('used_times');
            $table->unsignedTinyInteger('used_limit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
