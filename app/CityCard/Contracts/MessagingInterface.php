<?php

namespace App\CityCard\Contracts;

interface MessagingInterface
{
	public function message($to, $message);
}