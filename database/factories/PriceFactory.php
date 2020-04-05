<?php

use Faker\Generator as Faker;

$factory->define(App\Price::class, function (Faker $faker) {
    return [
        	'name' => $faker->sentence($nbWords = 3, $variableNbWords = true),
	        'description' => $faker->sentence($nbWords = 10, $variableNbWords = true),
	        //'evento_id' => $faker->,
	        'precio' => $faker->numberBetween($min = 500, $max = 5000), // 2,50 se escribe 250
    ];
});
