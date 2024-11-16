<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;


class SocilaLoginController extends Controller
{
    public function redirecttogoogle(){
        return Socialite::driver('google')->redirect();
    }

    public function redirecttofaceook(){
        return Socialite::driver('facebook')->redirect();
    }

    public function redirecttogithub(){
        return Socialite::driver('github')->redirect();
    }
    

    // the handel functions 

    public function googlecallback()
    {
        try {
            $user =Socialite::driver('google')->user();
            $findUser=User::where('social_id',$user->id)->first();
            if($findUser){
                Auth::login($findUser);
                $token = Auth::login($findUser);
                return response()->json([
                    'status' => 'success',
                    'message' => 'User login successfully',
                    'user' => $findUser,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            }else{
               $newUser = User::Create([
                    'email'=>$user->email,
                    'name'=>$user->name,
                    'social_id'=> $user->id,
                    'social_type' => 'google',
                    'password'=>Hash::make('my-google'),
               ]);
               Auth::login($newUser);
               $token = Auth::login($newUser);
               return response()->json([
                'status' => 'success',
                'message' => 'User created and login successfully',
                'user' => $newUser,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
          }
        }
        catch(Exception $x){
            dd($x->getMessage());
        }
    }


    public function githubcallback()
    {
        try {
            $user = Socialite::driver('github')->user();
            $findUser = User::where('social_id', $user->id)->first();
            if ($findUser) {
                Auth::login($findUser);
                $token = Auth::login($findUser);
                return response()->json([
                    'status' => 'success',
                    'message' => 'User login successfully',
                    'user' => $findUser,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            } else {
                $newUser = User::Create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'social_id' => $user->id,
                    'social_type' => 'github',
                    'password' => Hash::make('my-github'),
                ]);
                Auth::login($newUser);
                $token = Auth::login($newUser);
                return response()->json([
                    'status' => 'success',
                    'message' => 'User created and login successfully',
                    'user' => $newUser,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            }
        } catch (Exception $x) {
        dd($x->getMessage());
        }
    }   



    public function facebookcallback()
    {
        try {
            $user =Socialite::driver('facebook')->user();
            $findUser=User::where('social_id',$user->id)->first();
            if($findUser){
                Auth::login($findUser);
                $token = Auth::login($findUser);
                return response()->json([
                    'status' => 'success',
                    'message' => 'User login successfully',
                    'user' => $findUser,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            }else{
                $newUser = User::Create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'social_id' => $user->id,
                    'social_type' => 'facebook',
                    'password' => Hash::make('my-facebook'),
                ]);
                Auth::login($newUser);
                $token = Auth::login($newUser);
                return response()->json([
                    'status' => 'success',
                    'message' => 'User created and login successfully',
                    'user' => $newUser,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ]);
            }
        }
        catch(Exception $x){
            dd($x->getMessage());
        }
    }
}

