<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\userRequest;
use App\Http\Requests\userLoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\JwtAuth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Token;
use App\Mail\EmailVarify;
use App\Jobs\mailSendJob;
use Throwable;

class AuthController extends Controller
{
    function register(userRequest $request){
        try{
            //Request is valid, create new user
            $token    = $this->createToken($request->email);
            $url = 'http://127.0.0.1:8000/api/emailVarification/'.$token.'/'.$request->email;
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'url' => $url,
            ]);

            //Mail Send To Mail Trap Acc
            mailSendJob::dispatch($request->email, $user->url);
            return $user;
        } catch(Throwable $e){
            return response(['message' => $e->getMessage()]);
        }
    }

    function emailVarification($token,$email){
        try{    
            $emailVerify = User::where('email',$email)->first();
            if($emailVerify->email_verified_at != null){
                return response([
                    'message'=>'Already Verified'
                ]);
            }elseif ($emailVerify) {
                $emailVerify->email_verified_at = date('Y-m-d h:i:s');
                $emailVerify->save();
                return response([
                    'message'=>'Eamil Verified'
                ]);
            }else{
                return response([
                    'message'=>'Error'
                ]);
            }
        } catch(Throwable $e){
            return response(['message' => $e->getMessage()]);
        }
    }

    // function createToken($data) {
    //     try{
    //         $key = config('constantToken.secret');
    //         $payload = array(
    //             "iss" => "http://127.0.0.1:8000",
    //             "aud" => "http://127.0.0.1:8000/api",
    //             "iat" => time(),
    //             "nbf" => 1357000000,
    //             "data" => $data,
    //         );
    //         $jwt = JWT::encode($payload, $key, 'HS256');
    //         return $jwt;
    //     } catch(Throwable $e){
    //         return response(['message' => $e->getMessage()]);
    //     }
    // }

    function login(userLoginRequest $request) {
        try{
            //Check Eamil
            $data = [
                'email'    => $request->email,
                'password' => $request->password
            ];

            $user = User::where('email', $request->email)->first();
            //check if user already has token
            $var = Token::where('user_id', $user->id)->first();

            if(isset($var)){
                return response([
                    'message' => 'user already login',
                    'token' => $var['token']
                ]);
            }
            //Create User Token
            if(Auth::attempt($data)) {
                $token    =(new JwtAuth)->gettokenencode($user->id);
                $var      = Token::create([
                'user_id' => $user->id,
                'token'   => $token
            ]);
                return response([ 
                    'Status'  => '200',
                    'Message' => 'Successfully Login',
                    'Email'   => $request->email,
                    'token'   => $token
                ], 200);
            } else {
                return response([
                    'Status'  => '400',
                    'message' => 'Bad Request',
                    'Error'   => 'Email or Password does not match'
                ], 400); 
            }
        } catch(Throwable $e){
            return response(['message' => $e->getMessage()]);
        }    
    } 

    function logout(Request $request){
       try{
            //Decode Token
            $jwt = $request->bearerToken();
            $decoded =(new JwtAuth)->gettokendecode($jwt);
            $userID = $decoded->data;
            //Check If Token Exits
            $userExist = Token::where("user_id",$userID)->first();
            if($userExist){
                $userExist->delete();
            }else{
                return response([
                    "message" => "This user is already logged out"
                ], 404);
            }
                return response([
                    "message" => "logout successfull"
                ], 200);
        } catch(Throwable $e){
            return response(['message' => $e->getMessage()]);
        }
    }

    public function profile(Request $request){
        try{
            //Check User With Token
            $token = $request->bearerToken();
            $key = config('constantToken.secret');
            dd($key);
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            $userID = $decoded->data;
            $var = Token::where('user_id', $userID)->first();
            
            //Find User From With ID
            if(isset($var)) {
                $profile = User::find($userID);
                return response([ 
                    'Status'   => '200',
                    'email'    => $profile->email,
                    'password' => $profile->password
                ], 200);
            } else {
                return response([
                    'Status' => '400',
                    'message' => 'Bad Request',
                    'Error' => 'Incorrect userID = '.$userID
                ], 400); 
            }
        } catch(Throwable $e){
            return response(['message' => $e->getMessage()]);
        }
    }
}