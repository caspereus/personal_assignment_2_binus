<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;

Route::get('/', [HomeController::class,'index'])->name('home.index');

    Route::group(['middleware' => ['guest']], function() {
        Route::get('/register', [RegisterController::class, 'show'])->name('register.show');
        Route::post('/register', [RegisterController::class,'register'])->name('register.perform');
        Route::get('/login', [LoginController::class,'show'])->name('login.show');
        Route::post('/login', [LoginController::class,'login'])->name('login.perform');
        Route::get('/reload-captcha', [LoginController::class, 'reloadCaptcha']);
    });

    Route::group(['middleware' => ['auth']], function() {
        Route::get('/logout', [LogoutController::class, 'perform'])->name('logout.perform');
    });