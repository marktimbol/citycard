<?php

namespace App\Http\Controllers\Api\Auth\User;

use App\Events\User\UserRegistered;
use App\Http\Controllers\Controller;
use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'  => 'required',
            'email' => 'required|email|max:255|unique:users',
            'mobile'    => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {        
        return User::create([
            'name'  => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function register(Request $request)
    {                
        $validator = $this->validator($request->all());

        if( $validator->fails() ) {
            return response()->json([
                'authenticated' => false,
                'message' => $validator->errors()->first(),
                'user'  => []
            ]);
        }

        $user = $this->create($request->all());
        $user->load('photos', 'qrcode', 'reservations');
        
        event( new UserRegistered($user, $request->password) );
        
        $this->guard()->login($user);
        
        return response()->json([
            'authenticated' => true,
            'message'   => 'success',
            'user'  => UserTransformer::transform($user)
        ]);
    }
}
