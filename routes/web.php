<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return Inertia::render('client/Home');
});


Route::get('/', function () {
    return Inertia::render('auth/Login');
});

Route::get('/register', function () {
    return Inertia::render('auth/Register'); 
})->name('register');