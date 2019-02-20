<?php

use Faker\Generator as Faker;

$factory->define(App\Evento::class, function (Faker $faker) {
    
    $event_ini = $faker->dateTimeBetween($startDate = 'now', $endDate = '+2 month', $timezone = 'Europe/Madrid');

    return [
        // 'creator' => \App\Empresa::all()->first->id->get(),
        'nombre' => $faker->sentence($nbWords = 4, $variableNbWords = true),
        // 'photo' => $faker->, //debe elegir una ruta de entre los archives existentes de tipo 2 o vacío 
        'event_ini' => $event_ini,
        'event_fin' => $faker->dateTimeBetween($startDate = $event_ini, $endDate = '+1 day', $timezone = 'Europe/Madrid'),
        'aforo' => $faker->numberBetween($min = 100, $max = 90000),
        'destacado_ini' => $faker->dateTimeBetween($startDate = '-1 month', $endDate = $event_ini, $timezone = 'Europe/Madrid'),
        'destacado_fin' => $event_ini,
        'location_name' => $faker->city,
        'lat' => $faker->latitude($min = -90, $max = 90),
        'lng' => $faker->longitude($min = -180, $max = 180),
    ];
});
