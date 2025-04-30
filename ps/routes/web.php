<?php

use App\Http\Controllers\OidcAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('Auth/login');
});

Route::get('/denied', function () {
    return view('Auth/loginDenied');
});

Route::get('/callback', [OidcAuthController::class, 'handleCallback']);

Route::get('/home', function () {
    return view('/home');
});
