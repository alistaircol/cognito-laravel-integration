<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', \App\Http\Controllers\IndexController::class)->name('index');
Route::get('login', \App\Http\Controllers\LoginController::class)->name('login');
Route::any('login-success', \App\Http\Controllers\LoginSuccessController::class)->name('login.success');
Route::get('logout', \App\Http\Controllers\LogoutController::class)->name('logout');
Route::any('logout-success', \App\Http\Controllers\LogoutSuccessController::class)->name('logout.success');
