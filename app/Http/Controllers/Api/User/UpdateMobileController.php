<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateMobileController extends Controller
{
	public function update(Request $request)
	{
		$this->validate($request, [
			'mobile'	=> 'required'
		]);

		$user = auth()->guard('user_api')->user();

		$user->update([
			'mobile'	=> $request->mobile,
		]);

		return response()->json([
			'success'	=> true,
			'message'	=> 'Your mobile has been successfully updated.'
		]);
	}
}
