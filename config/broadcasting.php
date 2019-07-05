<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | This option controls the default broadcaster that will be used by the
    | framework when an event needs to be broadcast. You may set this to
    | any of the connections defined in the "connections" array below.
    |
    | Supported: "pusher", "redis", "log", "null"
    |
    */

    'default' => env('BROADCAST_DRIVER', 'null'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the broadcast connections that will be used
    | to broadcast events to other systems or over websockets. Samples of
    | each available type of connection are provided inside this array.
    |
    */

    'connections' => [

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                //
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

        'apn' => [
            'is_production' => env('APP_ENV') === 'production',
            'key_id' => env('APN_KEY_ID'),
            'team_id' => env('APN_TEAM_ID'), // The Team ID of your Apple Developer Account (available at https://developer.apple.com/account/#/membership/)
            'app_bundle_id' => env('APN_APP_BUNDLE_ID'), // The Bundle ID of your application. For example, "com.company.application"
            'private_key_path' => env('APN_PRIVATE_KEY', storage_path('AuthKey_G3GN5GNPA2.p8')),
            'private_key_secret' => env('APN_PRIVATE_KEY_SECRET'),
        ],

    ],

];
