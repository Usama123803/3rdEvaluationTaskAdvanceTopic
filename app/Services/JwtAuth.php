<?php 

namespace App\Services;

use Illuminate\Http\Request;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtAuth{

    protected $key;
    protected $payload;
    public function gettokenencode($data)
    {
        try{
            $key = config('constantToken.secret');
            $payload = array(
                "iss" => "http://127.0.0.1:8000",
                "aud" => "http://127.0.0.1:8000/api",
                "iat" => time(),
                "nbf" => 1357000000,
                "data" => $data,
            );
            $jwt = JWT::encode($payload, $key, 'HS256');
            return $jwt;
        } catch(Throwable $e){
            return response(['message' => $e->getMessage()]);
        }
    }

    public function gettokendecode($token)
    {
        $this->key=config('constantToken.secret');
        JWT::$leeway = 60;
        $decoded = JWT::decode($token, new Key($this->key,'HS256'));
        // $decoded_array = (array) $decoded;
        // $decoded_data = (array) $decoded_array;
        return $decoded;
    }
}