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
        $user = $event->user;

        $client = new Client([
            'headers'   => [
                'QB-Token'  => env('QB_Token', ''),
            ]
        ]);

        $client->post('https://api.quickblox.com/users.json', [
            'form_params'   => [
                'user[login]'   => $user->email,
                'user[password]'   => $event->password,
                'user[email]'   => $user->email,
                'user[full_name]'   => $user->name,
                'user[phone]'   => $user->phone,
            ]
        ]);       
    }
}
