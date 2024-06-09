<?php

namespace App\Listeners;

use App\Events\SaveLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Logs;

class SendSaveLog
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SaveLog $event): void
    {
        // save an event to the DB
        Logs::create([
            'name' => $event->payload['name'],
            'email' => $event->payload['email'],
            'action' => $event->payload['action']
        ]);
    }
}
