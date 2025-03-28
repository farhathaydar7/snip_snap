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

Route::get('/', function () {
    return view('welcome');
});

// Add a login route for web access
Route::get('/login', function () {
    return response()->json([
        'message' => 'Please use the API endpoint /api/auth/login for authentication',
    ], 401);
})->name('login');
