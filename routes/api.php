<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::resource('blogs','App\Http\Controllers\PostController');
// Route::name('verify')->get('users/verify/{token}','App\Http\Controllers\UserController@verify');

Route::post('/register',[App\Http\Controllers\UserController::class,'registration']);
Route::post('/login',[App\Http\Controllers\UserController::class,'login']);
Route::middleware('auth:api')->post('/logout',[App\Http\Controllers\UserController::class,'logout']);


Route::get('blogs/index', [App\Http\Controllers\PostController::class, 'index']);
Route::get('blogs/show', [App\Http\Controllers\PostController::class, 'show']);

Route::middleware('auth:api')->post('blogs/store', [App\Http\Controllers\PostController::class, 'store']);
Route::middleware('auth:api')->post('blogs/update/{post}', [App\Http\Controllers\PostController::class, 'update']);
Route::middleware('auth:api')->delete('blogs/{id}', [App\Http\Controllers\PostController::class, 'destroy']);

Route::middleware('auth:api')->get('users',[App\Http\Controllers\UserController::class, 'index']);
Route::middleware('auth:api')->get('/users/{id}',[App\Http\Controllers\UserController::class, 'show']);
Route::middleware('auth:api')->put('/users/{user}',[App\Http\Controllers\UserController::class, 'update']);
Route::middleware('auth:api')->delete('/users/{user}',[App\Http\Controllers\UserController::class, 'destroy']);
Route::middleware('auth:api')->post('/users/logout',[App\Http\Controllers\UserController::class, 'logout']);



