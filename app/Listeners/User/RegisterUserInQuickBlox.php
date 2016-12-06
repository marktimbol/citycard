<?php

namespace App\Listeners\User;

use App\Events\User\UserRegistered;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RegisterUserInQuickBlox
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
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        // $user = $event->user;

        // $response = request()->create('https://api.quickblox.com/users.json', 'POST', [
        //     'QB-Token'  => env('QB_Token', ''),
        //     'user[login]'   => $user->email,
        //     'user[password]'   => $user->password,
        //     'user[email]'   => $user->email,
        // ]);
        
        // dd($response);
    }
}
