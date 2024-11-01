<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\Users\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//the authintications route start
Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('profile', [AuthController::class, 'userProfile'])->middleware('auth:api');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
//the authintication routes end 

//start cart routes
Route::get('cart', [CartController::class, 'index'])->middleware('auth:api');
Route::post('cart/add', [CartController::class, 'addToCart'])->middleware('auth:api');
Route::post('cart/remove', [CartController::class, 'removeFromCart'])->middleware('auth:api');
Route::post('cart/deleteItem', [CartController::class, 'deleteFromCart'])->middleware('auth:api');
Route::post('cart/delete', [CartController::class, 'deleteCart'])->middleware('auth:api');
//end cart routes

