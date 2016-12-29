<?php

namespace App\CityCard;

use App\CityCard\Contracts\MessagingInterface;
use Illuminate\Support\Facades\Log;
use Twilio;

class TwilioSMS implements MessagingInterface
{
	public function message($to, $message)
	{
		try {
			Twilio::message($to, $message);
		} catch( \Services_Twilio_RestException $e ) {
			Log::info($e->getMessage());
		}
	}
}