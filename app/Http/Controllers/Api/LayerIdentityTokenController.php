<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CityCard\Layer\LayerIdentityTokenProvider;

class LayerIdentityTokenController extends Controller
{
	protected $layerIdentityTokenProvider;

	public function __construct()
	{
		$this->layerIdentityTokenProvider = new LayerIdentityTokenProvider();
	}
	
    public function store(Request $request)
    {
		return response()->json([
			'identity_token'	=> $this->layerIdentityTokenProvider->generateIdentityToken(
				$request->uuid, $request->nonce, $request->first_name,
				$request->last_name, $request->display_name, $request->avatar_url
			)
		]);
    }
}
