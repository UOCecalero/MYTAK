<?php

use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {

	// $gender = $faker->randomElement(['male', 'female']);
 //    function genderPreference(){
 //        if ($gender == 'male') { $genderpreference =  'female'; } else {  $genderpreference = 'male'; }
 //            return $genderpreference;
 //    }
    

    return [
        'name' => $faker->firstName(),
        'surnames' => $faker->lastName,
        // 'gender' => $gender,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => str_random(10),
        'lema' => $faker->sentence($nbWords = 6, $variableNbWords = true),
        //'devicetoken' => $faker->name,
        'last_connection' => $faker->dateTimeThisMonth($max = 'now', $timezone = null),
        'photo' => 'http://i.pravatar.cc/300?'.$faker->numberBetween($min = 1, $max = 70), //tiene que elegir de entre los archives de tipo 1 que hay subidos
        'birthdate' => $faker->date($format = 'Y-m-d', $max = '-18 year'),
        'job' => $faker->jobTitle,
        'studies' => $faker->word,
        // 'genderpreference' =>  genderPreference(), //male, female, both
        'lat' => $faker->latitude($min = -90, $max = 90),
        'lng' => $faker->longitude($min = -180, $max = 180),

    ];
});
