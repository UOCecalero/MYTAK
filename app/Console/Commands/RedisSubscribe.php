<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscription to all the Redis channels: messages, alets and admin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Redis::subscribe(['ports'], function ($helloMessage) {
        //     Hellomessage es un mensaje que debe contener emisor con su token mas el puerto por el que ha llegado
        //     event(new ClientConnected($helloMessage));
        // });

        Redis::subscribe(['incomeMessage'], function ($tokenAndMessage) {
             
             $data = json_decode($tokenAndMessage);
             $token = $data->token;
             $message = $data->message;
             event(new \App\Events\MensajeRecibido($token, $message));
        });

        //Alerts no necesita estar suscrito ya que es canal solo de salida
        // Redis::subscribe(['alerts'], function ($message) {
        //     echo $message;
        // });

        //Admin no necesita estar suscrito ya que es canal solo de salida
        // Redis::subscribe(['admin'], function ($message) {
        //     echo $message;
        // });

        
    }
}
