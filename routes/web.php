<?php


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware(['auth', 'role:admin'])->group(function () {
});

Route::middleware('auth')->group(function () {
});

require __DIR__.'/auth.php';

