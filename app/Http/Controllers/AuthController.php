<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' =>'required | string',
            'email' =>'required | string | email |unique:users,email',
            'password' =>'required | min:6 | regex:"/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/" | confirmed'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'admin' => 'false'        //toti noii useri vor fii de tip admin:'false' deoarece am creat deja prin seeds 2 conturi de admin si nu mai vreau altele noi
        ]);

        $token = $user->createToken('mytoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request)
     {

        $request->validate([
            'email' => 'required | email',
            'password' => 'required '
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return [
                'message' => 'Email or password are incorrect!'
            ];
        }
        
        $token = $user->createToken('mytoken')->plainTextToken;

        $response = [
            'user' =>  $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out!'
        ];
    }

}
