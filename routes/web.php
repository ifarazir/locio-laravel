<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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
    return view('welcome');
});

// route for show user ip
Route::get('/user-ip', function (Request $request) {
    // get user ip by forworded for header
    $ip = $request->header('x-forwarded-for');
    // if user ip is not set in header
    if (!$ip) {
        // get user ip by server remote address
        $ip = $request->server('REMOTE_ADDR');
    }
    // return user ip
    // slice before ,
    $ip = explode(',', $ip)[0];
    return $ip;
});
