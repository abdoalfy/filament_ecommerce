<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocilaLoginController;
use App\Http\Controllers\Users\AuthController;
use App\Mail\UserRegister;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
   // dd( storage_path('app/public'), env('APP_URL').'/storage');
    return view('welcome');
});

Route::get('/art', function () {
    Artisan::call('optimize');
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//google
Route::get('auth/google',[SocilaLoginController::class,'redirecttogoogle']);
Route::get('auth/google/callback',[SocilaLoginController::class,'googlecallback']);


//github
Route::get('auth/github',[SocilaLoginController::class,'redirecttogithub']);
Route::get('auth/github/callback',[SocilaLoginController::class,'githubcallback']);


//facebook
Route::get('auth/facebook',[SocilaLoginController::class,'redirecttofaceook']);
Route::get('auth/facebook/callback',[SocilaLoginController::class,'facebookcallback']);

require __DIR__.'/auth.php';


