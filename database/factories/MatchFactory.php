<?php

use Faker\Generator as Faker;

$factory->define(App\Match::class, function (Faker $faker) {
    return [

    	// 'usuario1_id' => $this->getRandomUserId($emisor),
        // 'usuario2_id' => $this->getRandomUserId($emisor),
        // 'evento_id' => $this->getRandomEventoId,
        'es_aceptado' => $faker->optional($weight = 0.4)->randomElement([true, false]),
    ];
});
