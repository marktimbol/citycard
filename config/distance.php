<?php

return [

	// The outlets near the user within 15km
	'near_outlets'	=> env('NEAR_OUTLETS_DISTANCE', 15), // 15km

	// The clerk should be in this given range in order to Login
	'outlet_range'	=> env('OUTLET_RANGE', 0.500), // 500m

];