<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['generateAccessToken', 'register', 'login']]);
    }

    protected function generateAccessToken($user)
    {
        $token = $user->createToken($user->email.'-'.now());

        return $token->accessToken;
    }

    public function login(Request $request)
    {
        $data = $request->all();

        $this->validator($data)->validate();

        if( Auth::attempt(['email'=>$request->email, 'password'=>$request->password]) ) {
            $user = Auth::user();

            $token = $user->createToken($user->email.'-'.now());

            return response()->json([
                'token' => $token->accessToken,
                'user' => $user,
            ]);
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
           Auth::user()->AauthAcessToken()->delete();
        }

        return response()->json([
            'message' => 'Logged out complete'
        ], 200);
    }

    /**
     * Get a validator for an incoming request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|exists:users,email', 
            'password' => 'required'
        ]);
    }
}
