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

        $redisSubscribe = Redis::connection();
        // $redisPorts = new \Predis\Client([
        //     'host'     => '127.0.0.1', 
        //     'password' => null, 
        //     'database' => 0,
        //     // 'read_write_timeout' => 0,
        //     ]); 

        $redisSubscribe->subscribe(['incomeMessage', 'outcomeMessage', 'ports' ], function ($message, $channel) {

            switch ($channel) {
                case 'ports':
                    //message contiene el token del usuario conectado
                    event(new \App\Events\ClientConnected($message));
                break;
                
                case 'incomeMessage':
                    //message contiene token (emisor), mensaje, receptor y puerto (socket emisor)
                    event(new \App\Events\MensajeRecibido($message));
                break;

                default:
                    echo "outcomeMessage \n";
                break;
            }
  
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
