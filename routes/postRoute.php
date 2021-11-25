<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

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

Route::group(['middleware'=>['UserMiddleware']],function(){
	Route::post('create',[PostController::class,'create']);
	Route::get('profile',[AuthController::class,'profile']);
	Route::get('show',[PostController::class,'show']);
	Route::post('update/{id}',[PostController::class,'update']);
	Route::post('delete/{id}',[PostController::class,'delete']);
});