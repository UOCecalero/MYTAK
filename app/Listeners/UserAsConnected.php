<?php

namespace App\Listeners;

use App\Events\ClientConnected;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        //
    }
}
