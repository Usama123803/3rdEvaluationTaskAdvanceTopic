<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Services\JwtAuth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Token;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {   
        try {

            $decoded=(new JwtAuth)->gettokendecode($request->bearerToken());
            $request=$request->merge(array('dataMiddleware'=>$decoded));
             return $next($request);
        } catch (Exception $e) {
            
        }
    }
}
