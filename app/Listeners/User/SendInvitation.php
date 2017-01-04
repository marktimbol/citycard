<?php

namespace App\Listeners\User;

use App\Events\User\UserInvitesAFriend;
use App\Mail\InvitationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendInvitation implements ShouldQueue
{
    use InteractsWithQueue;

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
     * @param  UserInvitesAFriend  $event
     * @return void
     */
    public function handle(UserInvitesAFriend $event)
    {
        $invitation = $event->invitation;

        Mail::to($invitation->email)->send(new InvitationEmail($invitation));
    }
}
