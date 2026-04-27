<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/account/login', [AccountController::class, 'login'])->name('account.login');
Route::get('/account/register', [AccountController::class, 'registration'])->name('account.registration');
Route::post('/account/process-registration', [AccountController::class, 'processRegistration'])->name('account.processRegistration');


