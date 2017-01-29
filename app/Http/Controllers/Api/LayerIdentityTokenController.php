<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Layer\LayerIdentityTokenProvider;

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
			'identity_token'	=> $this->layerIdentityTokenProvider->generateIdentityToken($request->uuid, $request->nonce)
		]);
    }
}
