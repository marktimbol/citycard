<?php

namespace App\Http\Controllers\Api\Auth\Merchant;

use App\Http\Controllers\Controller;
use App\Merchant;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $credentials = $this->credentials($request);
        $attempt = Auth::guard('merchant')->attempt($credentials);
            
        if( $attempt ) {
            return response()->json([
                'authenticated' => true,
                'message'   => 'success',
                'auto_logout' => false,
                'merchant'  => auth()->guard('merchant')->user()
            ]);
        }

        return response()->json([
            'authenticated' => false,
            'message' => 'Invalid email or password.',
            'merchant'  => []
        ]);
    }

}
