<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\PasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// fddffsf@fddffddff.fdf
// johnybeg@notmail.con

// API Endpoints
Route::middleware(['api'])->group( function () {

        Route::get('/', function () {
            return 'Territory Services API Version 1.0';
        });

        Route::post('/signup', [ApiController::class, 'signup']);

        Route::post('/signin', [ApiController::class, 'signin']);

        Route::get('/auth-user', [ApiController::class, 'authUser']);

        Route::group(['namespace' => 'Auth'], function () {
            Route::get(
                '/password-reset/{lang}/{token?}',
                [PasswordController::class, 'getReset']
            );
            Route::post(
                '/password-reset/{lang}',
                [PasswordController::class, 'postEmailApi']
            );
        });
    }
);