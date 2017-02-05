<?php

namespace App\CityCard\Layer;

use Namshi\JOSE\SimpleJWS;

class LayerIdentityTokenProvider
{
    private $providerID;
    private $keyID;
    private $privateKey;

	public function __construct()
	{
        $this->providerID = getenv('LAYER_PROVIDER_ID');
        $this->keyID      = getenv('LAYER_PRIVATE_KEY_ID');
        $this->privateKey = getenv('LAYER_PRIVATE_KEY');
	}

    /**
     * Checks if all the proper config has been prvided.
     */
    private function checkLayerConfig()
    {
        $errorString = array();
        if ($this->providerID == '') {
            array_push($errorString, 'LAYER_PROVIDER_ID');
        }

        if ($this->keyID == '') {
            array_push($errorString, 'LAYER_PRIVATE_KEY_ID');
        }

        if ($this->privateKey == '') {
            array_push($errorString, 'LAYER_PRIVATE_KEY');
        }

        if (count($errorString) > 0) {
            $joined = implode(',', $errorString);
            trigger_error("$joined  not configured. See README.md", E_USER_ERROR);
        }
    }

    public function generateIdentityToken($user_id, $nonce, $first_name, $last_name, $display_name, $avatar_url)
    {        
        $this->checkLayerConfig();

        $jws = new SimpleJWS(array(
            // String - Expresses a MIME Type of application/JWT
            'typ' => 'JWT',
            // String - Expresses the type of algorithm used to sign the token;
            // must be RS256
            'alg' => 'RS256',
            // String - Express a Content Type of Layer External Identity Token,
            // version 1
            'cty' => 'layer-eit;v=1',
            // String - Private Key associated with "layer.pem", found in the
            // Layer Dashboard
            'kid' => $this->keyID
        ));

        $jws->setPayload(array(
            // String - The Provider ID found in the Layer Dashboard
            'iss' => $this->providerID,
            // String - Provider's internal ID for the authenticating user
            'prn' => $user_id,
            // Integer - Time of Token Issuance in RFC 3339 seconds
            'iat' => round(microtime(true) * 1000),
            // Integer - Token Expiration in RFC 3339 seconds; set to 2 minutes
            'exp' => round(microtime(true) * 1000) + 120,
            # The nonce obtained via the Layer client SDK.
            'nce' => $nonce,
			"first_name" => $first_name, // Optional String: First name for the user
			"last_name" => $last_name, // Optional String: Last name for the user
			"display_name" => $display_name, // Optional String: The name to show onscreen for the user
			"avatar_url" => $avatar_url // Optional String: URL to profile photo for the user
        ));
        
        $privateKey = openssl_pkey_get_private($this->privateKey);

        $jws->sign($this->privateKey);
        $identityToken = $jws->getTokenString();

        return $identityToken;
    }    
}