<?php

use Faker\Generator as Faker;

$factory->define(App\Empresa::class, function (Faker $faker) {

	$empresaName = $faker->word;
    return [
        'last_connection' => $faker->dateTime($max = 'now', $timezone = 'Europe/Madrid'),
        'name' => $empresaName,
        //'creator' => $faker,
        'email' => $faker->unique()->companyEmail,
        'pwd' => $password = bcrypt('secret'),
        'web' => 'https://www.' . $empresaName . '.com',
    ];
});
