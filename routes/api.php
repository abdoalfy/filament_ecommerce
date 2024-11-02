<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
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


//start order routes
Route::get('order',[CheckoutController::class,'index'])->middleware('auth:api');
Route::post('copoun',[CheckoutController::class,'applyCoupon'])->middleware('auth:api');
//start order routes

//start Payment routes
Route::get('myfatoorah/success',[CheckoutController::class,'success'])->name('myfatoorah.success');
Route::get('myfatoorah/error',[CheckoutController::class,'error'])->name('myfatoorah.error');
//end Payment routes


//start route for signle category 
Route::get('category/{slug}',[ProductController::class,'singleCategory'])->name('single.category');
Route::get('product/{slug}',[ProductController::class,'singleProduct'])->name('single.product');
Route::get('categories',[ProductController::class,'allCategories'])->name('all.categories');
