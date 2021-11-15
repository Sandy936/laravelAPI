<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function adminlogin(Request $request){
        $fields = $request->validate([            
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

       // Check email
       $user = User:: where('email',$fields['email'])->first();

       // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)){
            return response([
                'message' => 'Bad creds'
            ], 401);           

        }
        $token = $user->createToken('myapptoken')->plainTextToken;
        $reponse = [
            'user' => $user,
            'token' => $token
            
        ];
        return response($reponse, 201);
    }
    public function logout(Request $request){
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out successfully.'
        ];

    }
}
