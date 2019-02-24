<?php
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\Evento::class, function (Faker $faker) {
    
    $event_ini = $faker->dateTimeBetween($startDate = 'now', $endDate = '+2 month', $timezone = 'Europe/Madrid');

    $starts_at = Carbon::createFromTimestamp($event_ini->getTimeStamp());

    return [
        // 'creator' => \App\Empresa::all()->first->id->get(),
        'nombre' => $faker->sentence($nbWords = 4, $variableNbWords = true),
        // 'photo' => $faker->, //debe elegir una ruta de entre los archives existentes de tipo 2 o vacío 
        'event_ini' => $event_ini,
        // 'event_fin' => $faker->dateTimeBetween($startDate = $event_ini, $endDate = '+1 day', $timezone = 'Europe/Madrid'),
        'event_fin' => $faker->dateTimeBetween($startDate = $event_ini, $endDate = Carbon::createFromFormat('Y-m-d H:i:s', $starts_at)->addHours( $faker->numberBetween( 5, 12 ) ), $timezone = 'Europe/Madrid'),
        'aforo' => $faker->numberBetween($min = 100, $max = 90000),
        // 'destacado_ini' => $faker->optional($weight = 0.2)->dateTimeBetween($startDate = '-1 month', $endDate = $event_ini, $timezone = 'Europe/Madrid'),
        // 'destacado_fin' => $faker->optional($weight = 0.1)->$event_ini,
        'location_name' => $faker->city,
        'lat' => $faker->latitude($min = -90, $max = 90),
        'lng' => $faker->longitude($min = -180, $max = 180),
    ];
});
