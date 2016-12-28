<?php

namespace App\Listeners\User;

use App\CityCard\Contracts\MessagingInterface;
use App\Events\User\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendSixDigitMobileVerification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $sms;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(MessagingInterface $sms)
    {
        $this->sms = $sms;
    }
    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        $mobile = $event->user->mobile;
        $verification_code = $event->user->verification_code;
        $message = sprintf('Your City Card verification code is %s', $verification_code);

        $this->sms->message($mobile, $message);
    }
}
