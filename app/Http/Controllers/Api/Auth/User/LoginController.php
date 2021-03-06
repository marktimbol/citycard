<?php

namespace App\Http\Controllers\Api\Auth\User;

use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use App\User;
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
        // $attempt = Auth::attempt($credentials);
        $attempt = Auth::guard('user')->attempt($credentials);
            
        if( $attempt ) {
            $user = auth()->guard('user')->user();
            $user->load('photos', 'qrcode', 'reservations');

            return response()->json([
                'authenticated' => true,
                'message'   => 'success',
                'user'  => UserTransformer::transform($user)
            ]);
        }

        return response()->json([
            'authenticated' => false,
            'message' => 'Invalid email or password.',
            'user'  => []
        ]);
    }

}
