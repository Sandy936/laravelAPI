<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\VerifyUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Mail\SendPinMail;

class AuthController extends Controller
{
    public function register(Request $request){
        $fields = $request->validate([
            'name' => 'required|string',
            'user_role' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'user_role' => $fields['user_role'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;
        $reponse = [
            'user' => $user,
            'token' => $token
            
        ];

        return response($reponse, 201);


    }

    public function login(Request $request){
        $fields = $request->validate([            
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

       // Check email
       $user = User:: where('email',$fields['email'])
                    -> where('verified',1)
                    ->first();


       // Check password
        if($user && Hash::check($fields['password'], $user->password)){

            $token = $user->createToken('myapptoken')->plainTextToken;
            $status = $user;                   

        }
        else {
            $status ="Wrong email or password!";  
        } 
        
        $reponse = [
            'message' => $status,
            'API bearer token' => $token
            
        ];

        
        //$user=Session::get('user');
        //return $user->id; 
        return response($reponse, 201);

    }

    public function logout(Request $request){
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];

    }
  

    /**
     * Serach for name.
     *
     * @param  str  $token
     * @return \Illuminate\Http\Response
     */
    public function signup(Request $request,$token)
    {

        $url = url('api/verify-pin');
        $fields = $request->validate([            
            'user_name' => 'required|string|unique:users,user_name|min:4|max:20',
            'password' => 'required|string|confirmed'
        ]);

        // Check email
        $verifyUser = VerifyUser::where('token', $token)->first();
        if(isset($verifyUser) ){
            if(($verifyUser->user->verified) != 1){           

            $user = $verifyUser->user;
            $verifyUser->user->user_name = $fields['user_name'];
            $verifyUser->user->password = bcrypt($fields['password']);
            $verifyUser->user->user_role = 'user';
           
            $randomNumber = random_int(100000, 999999);
            $verifyUser->user->sixdigitpin = $randomNumber;
            $verifyUser->user->save();
            

            Mail::to($verifyUser->user->email)->send(new SendPinMail($user));
            $status = "We have sent you a six digit verfication code to your email ID. Use this code to this url ".$url." to complete the registration.";
            }
            else{
                $status = 'Already verified';
                $randomNumber = '';
            }
        }
        else{
            $status = '';
            $randomNumber = '';
        }
        
        $response = [
            'message' => $status,
            'API six digit pin' => $randomNumber
            
        ];
        return response($response, 201);     
        
        
    }



    public function verifypin(Request $request){
        $fields = $request->validate([            
            'email' => 'required|string',            
            'pin' => 'required|integer',
        ]);

       // Check email
       $user = User:: where('email',$fields['email'])
                        ->where('sixdigitpin',$fields['pin'])
                        ->first();

        if(isset($user) ){
            if(!$user->verified) {
                $user->verified = 1;
                $user->registered_at = new \DateTime();
                $user->save();
                $status = "Successfully Registered now you can login to update your profile.";
                $token = $user->createToken('myapptoken')->plainTextToken;

            }
            else{
                $status = "Your account is already verified.";
                $token = $user->createToken('myapptoken')->plainTextToken;
                
            }
        }
        else {
            $status = "Email or Pin is not correct";  
            $token = '';  
        }       

        // $token = $user->createToken('myapptoken')->plainTextToken;
        $reponse = [
            'message' => $status
            
            
        ];

        return response($reponse, 201);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateprofile(Request $request,$id){
        $fields = $request->validate([
            'name' => 'required|string',       
            'email' => 'required|string|unique:users,email',
            'file' => 'required|mimes:png,jpg,jpeg|max:200|dimensions:max_width=256,max_height=256'
            
        ]);

        if ($file = $request->file('file')) {
            $path = $file->store('public/uploads');
            $name = $file->getClientOriginalName();
        }
        
        $user = User::find($id);
        $user->email = $request->email;
        $user->avatar = $path;
        $user->save();
        return $user;        
        
    }

}
