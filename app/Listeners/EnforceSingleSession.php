<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
class EnforceSingleSession
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
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {

     //   $currentId = Session::getId();
//        DB::table('sessions')
//            ->where('user_id', $event->user->getKey())
//            ->where('id', '!=', $currentId)
//            ->delete(); // выкинули все другие браузеры/устройства

//        $user = $event->user;
//        $newId = Session::getId();
//
//        if ($user->current_session_id && $user->current_session_id !== $newId) {
//            Session::getHandler()->destroy($user->current_session_id); // гасим старую
//        }
//        $user->forceFill(['current_session_id' => $newId])->save();
    }
}
