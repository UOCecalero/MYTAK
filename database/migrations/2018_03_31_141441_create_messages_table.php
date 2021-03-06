<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            //$table->unsignedBigInteger('conversation'); //Hace referencia a un match 
            $table->unsignedBigInteger('emisor');
            $table->unsignedBigInteger('receptor');
            $table->text('receptor_token')->nullable();
            $table->text('texto');
            $table->boolean('checked')->default(false);
            $table->boolean('caducado')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
