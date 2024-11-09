<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\PaymentMethodsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TestMoniaController;
use App\Http\Controllers\Users\AuthController;
use App\Http\Middleware\SetLanguage;
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
Route::post('updateProfile', [AuthController::class, 'updateProfile'])->middleware('auth:api');
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


//start route for products and categories 
Route::get('category/{slug}',[ProductController::class,'singleCategory'])->name('single.category')->middleware(SetLanguage::class);
Route::get('product/{slug}',[ProductController::class,'singleProduct'])->name('single.product')->middleware(SetLanguage::class);
Route::get('categories',[ProductController::class,'allCategories'])->name('all.categories')->middleware(SetLanguage::class);
Route::get('products',[ProductController::class,'allProducts'])->name('all.products')->middleware(SetLanguage::class);
//end route for products and categories 


//start routes for test monia 
Route::get('testmonia',[TestMoniaController::class,'index'])->middleware(SetLanguage::class);
//end routes for test monia 

//start favorite routes
Route::get('favorites',[FavoriteController::class,'index'])->middleware(SetLanguage::class);
Route::post('changeFavorite',[FavoriteController::class,'changeFavorite'])->middleware('auth:api');
Route::get('userFav',[FavoriteController::class,'userFav'])->middleware('auth:api',SetLanguage::class);
//end favorite routes 


//start active payment methods route
Route::get('activePaymentMethods',[PaymentMethodsController::class,'allPaymentMethods']);
//end active payment method route