<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct() {
    }
    public function me()
     {      
        $user = User::where('id', auth()->user()->id)->first();
        $user['allPermissions'] = $user->getAllPermissions()->pluck('name');
        return response()->json(compact('user'));
    }

    public function login(Request $request)
    {
        if($token =auth()->attempt(['email' => $request['email'], 'password' =>$request['password'], 'active' => 1])){
           $user = $request->user();
           return response()->json(compact('token', 'user'));
        }
        return response()->json(['message' => 'Invalid login credential'], 401);
    }
    public function logout()
    { 
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
