<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return view('mainpage.index');
});

Route::resource('events', EventController::class);
