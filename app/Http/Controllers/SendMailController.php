<?php

namespace App\Http\Controllers;

use App\Mail\VerifyMail;
use App\Models\User;
use App\Models\VerifyUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;


class SendMailController extends Controller
{
    
    protected function sendmail(Request $data)
    {

        $user = User::create([           
            'email' => $data['email']           
        ]);

        $verifyUser = VerifyUser::create([
            'user_id' => $user->id,
            'token' => Str::random(40)
        ]);

        Mail::to($user->email)->send(new VerifyMail($user));
        $status = "Email has been sent successfully to ".$data['email'];
        $sentitem = url('sign-up', $user->verifyUser->token);
        //return $user;

        return [
            'message' => $status,
            'API signup link' => $sentitem
        ];
        
    }
}
