<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ScheduleController;

Route::get('/', function () {
    return view('mainpage.landing');
});

Route::get('/schedules', [ScheduleController::class, 'index'])
    ->middleware('auth')
    ->name('schedules.index');

Route::resource('events', EventController::class)->middleware('auth');

// landingpage
Route::view('/', 'mainpage.landing')->name('landing');

// auth 
Route::get('/login', [AuthController::class, 'showLogin'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->middleware('guest');

Route::get('/events', [EventController::class, 'index'])->name('events.index');

// logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

    use App\Http\Controllers\ProfileController;

Route::get('/profile', [ProfileController::class, 'show'])
    ->middleware('auth')
    ->name('profile.show');