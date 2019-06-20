<?php

namespace App\Listeners;

use App\Events\ClientConnected;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class UserAsConnected
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ClientConnected  $event
     * @return void
     */
    public function handle(ClientConnected $event)
    {

        $client = new \GuzzleHttp\Client();
        $httpResponse = $client->get( env('URL')'api/me', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer '.$event->token,
                ]
            ]);

        if ( $httpResponse->getStatusCode() != 200 ){ /** Lanza un error **/ }
        $userConnected = json_decode( $httpResponse->getBody()->getContents());
     
        $redisCache = Redis::connection('cache');
        if ( $redisCache->exists("user:".$userConnected->id) ) { 
                 $oldPort = $redisCache->get("user:".$userConnected->id);
                 $redisCache->del("port:".$oldPort);

         }

         $blockCommands = $redisCache->pipeline();
         $blockCommands->set("user:".$userConnected->id, $event->port);
         $blockCommands->expire("user:".$userConnected->id, 40);

         $blockCommands->set("port:".$event->port, $userConnected->id);
         $blockCommands->expire( "port:".$event->port, 40);

         $blockCommands->execute();
         $redisCache->quit();

         echo "SET user:".$userConnected->id." in port ".$event->port."\n";
    }
}
