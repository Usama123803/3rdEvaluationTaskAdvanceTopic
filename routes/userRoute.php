<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// public route
Route::post('register',[AuthController::class,'register']);
Route::get('emailVarification/{token}/{email}',[AuthController::class,'emailVarification']);
Route::post('login',[AuthController::class,'login']);
Route::post('logout',[AuthController::class,'logout']);
Route::get('profile',[AuthController::class,'profile']);
Route::post('userUpdate/{id}',[AuthController::class,'userUpdate']);