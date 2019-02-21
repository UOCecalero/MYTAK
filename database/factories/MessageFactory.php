<?php

use Faker\Generator as Faker;

$factory->define(App\Message::class, function (Faker $faker) {
    
    // $emisor = $this->getRandomUserId(null);

    return [

    	// 'emisor' => $emisor
        // 'receptor' => $this->getRandomUserId($emisor);
        'texto' => $faker->sentence($nbWords = 10, $variableNbWords = true),
           
    ];
});
