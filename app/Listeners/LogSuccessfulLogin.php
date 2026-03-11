<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $ip = request()->ip();
        $time = date('d-m-Y H:i:s',time());
        $serverName = $_SERVER['SERVER_NAME'] ?? 'unknown';



        Log::channel('login')->info ([
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $ip,

            'server' => $serverName,
            'time' => $time,
        ]);

    }
}
