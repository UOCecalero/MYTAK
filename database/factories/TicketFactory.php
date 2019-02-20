<?php

use Faker\Generator as Faker;

$factory->define(App\Ticket::class, function (Faker $faker) {
    

    return [

	    	//'random' => random_int(1,65535),
	        // 'evento_id' => DEPENDE DE PRICE,
	        // 'user_id' => $this->getRandomEventoId(),
	        // 'price_id' => $this->getRandomUserId(),
	        // 'qr' => //Se genra en orígen


	         // 'hash' => ,
    		//Forma como se genera el hash
    		//$concat = $random.$ticket->id.$type->id.$ticket->created_at.$evento->id.$user->id;
            // $hash = hash("md5", $random.$ticket->id.$type->id.$ticket->created_at.$evento->id.$user->id);
        	
    ];
});
