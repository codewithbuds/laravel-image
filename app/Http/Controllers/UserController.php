<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserVerify;
use DB;
use Hash;
use Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
     //Match credentials and login
     public function Login(UserRequest $request)
     {
         $request->validated();
 
         $credentials = $request->only('email', 'password');
         if (Auth::attempt($credentials)) {
             return response()->json(['message' => 'You have logged in succssfull']);
         }
 
         return response()->json(['message'=> 'You have entered invalid credentials']);
     }
 
     // saves user data for signup
     public function Registration(UserRequest $request)
     {
         $request->validated();
 
         $user = $request->all();
         
        return $createUser = $this->create($user);
 
         $token = Str::random(255);
 
         UserVerify::create([
             'user_id' => $createUser->id,
             'token' => $token,
         ]);

        //  DB::table('users')->insert([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'profileimage' => $request->profileimage,
        //     'password' => $request->password,
        // ]);
     }

         // create table of user
     public function create(array $user)
     {
         $user= User::create([
             'name' => $user['name'],
             'email' => $user['email'],
             'profileimage' => $user['profileimage'],
             'password' => Hash::make($user['password']),
         ]);

        
 
        //  Image_Upload
        //  if ($request->profileimage) {
        //      $imageName = time() . '.' . $request->profileimage->extension();
        //      $request->profileimage->move(public_path('images'), $imageName);
        //  }
 
         // Here I can send mail through Queues or like this 
         Mail::send(
             'email.emailVerificationEmail',['token' => $token],
             function ($message) use ($request) 
             {
                 $message->to($request->email);
                 $message->subject('Email Verification Mail');
             });
             
         return response()-> json(['message' => 'Registered Successfully', 'user' =>$user, 'token' => $user->$token]);
     }
 
     //Verifies the account 
     public function verifyAccount($token)
     {
         $verifyUser = UserVerify::where('token', $token)->first();
 
         $message = 'Sorry your email cannot be identified.';
 
         if (!is_null($verifyUser)) {
             $user = $verifyUser->user;
 
             if (!$user->is_email_verified) {
                 $verifyUser->user->is_email_verified = 1;
                 $verifyUser->user->save();
                 $message = 'Your e-mail is verified. You can now login.';
             } else {
                 $message =
                     'Your e-mail is already verified. You can now login.';
             }
         }
     }
     
 
       //LoggedIn User's View Profile
     public function profile(Request $request)
     {
         $token = $request->header('Authorization');
         $user = UserVerifiy::where('token', $token)->first();
         $user = $user->user;
         if($user)
         {
             return response()->json(['message' =>"My Profile", 'user' => $users]);
         }
         else{
             return response()->json(['message' =>"User Not Found"]);
         }   
     }
}
