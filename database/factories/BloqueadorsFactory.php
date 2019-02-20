<?php

use Faker\Generator as Faker;

$factory->define(App\Boqueadors::class, function (Faker $faker) {
    return [
        //'user_id' => >$this->getRandomUserId(),
        //'bloqueador_id' => $this->getRandomUserId($emisor) ,
        'bloqueador_type' => 'usuario'
    ];
});
