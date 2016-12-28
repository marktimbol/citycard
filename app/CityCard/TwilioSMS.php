<?php

namespace App\CityCard;

use Twilio;
use App\CityCard\Contracts\MessagingInterface;

class TwilioSMS implements MessagingInterface
{
	public function message($to, $message)
	{
		Twilio::message($to, $message);
	}
}