<?php

namespace App\Listeners;

use App\Events\MensajeRecibido;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class EnviarMensajeDestinatario
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
     * @param  MensajeRecibido  $event
     * @return void
     */
    public function handle(MensajeRecibido $event)
    {
            $redis = Redis::connection('cache');
            $emisorId = $redis->get('port:'.$event->port);
            $redis->quit();

            $emisor = \App\User::findOrFail($emisorId);
            $receptor = \App\User::findOrFail($event->receptor);

            echo "MESSAGE from user ".$emisor->id." to user ".$receptor->id."\n";

            //Comprobamos que el receptor no lo tenga bloqueado
            if ( $emisor->bloqueadors->where('id', $receptor->id )->count() > 0 ){
            return;
            }

            if ( $emisor->matches()->where('id', $receptor->id )->count() > 0 ){

            $message = new \App\Message();

            $message->emisor = $emisor->id;
            $message->receptor = $receptor->id;
            $message->receptor_token = $receptor->tokens[0]->id; //Alternativa sin almacenar $message["receptor_token"] = $receptor_token;
            $message->texto = $event->message;

            $message->save();

            $time = $message->created_at;
            $message["time"] = $time->toTimeString();

            $redisPublishMessages = new \Predis\Client();
            $redisPublishMessages->publish('outcomeMessage', json_encode($message) );
            $redisPublishMessages->quit();

            echo "MESSAGE SENT :".$message."\n";

        } else {   echo "EMISOR and RECEPTOR are NOT MATCHED \n";   }
      
    }
}

