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
    return view('api-home');
});

// API Docs
Route::get('/docs', function () {    
    $domain = isset($_SERVER['HTTP_HOST']) 
    ? $_SERVER['HTTP_HOST'] 
    : $_SERVER['SERVER_NAME'];
    return view('docs')->with('api_url', 'http://' . $domain . '/v1');
});

