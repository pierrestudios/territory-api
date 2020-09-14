<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PublishersController;
use App\Http\Controllers\TerritoriesController;

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

        Route::post('/signup', [ApiController::class, 'signup']);
        Route::post('/signin', [ApiController::class, 'signin']);
        Route::get('/auth-user', [ApiController::class, 'authUser']);
        Route::get('/activities', [ApiController::class, 'activities']);
        Route::get('/validate', [ApiController::class, 'validateServerURL']);

        Route::get('/users', [PublishersController::class, 'users']);
        Route::post('/users/{userId}/save', [PublishersController::class, 'saveUser']);
        Route::post('/users/{userId}/delete', [PublishersController::class, 'deleteUser']);
         
        Route::get('/publishers', [PublishersController::class, 'index']);
        Route::post('/publishers/filter', [PublishersController::class, 'filter']);
        Route::get('/publishers/{publisherId}', [PublishersController::class, 'view']);
        Route::post('/publishers/add', [PublishersController::class, 'add']);
        Route::post('/publishers/attach-user', [PublishersController::class, 'attachUser']);
        Route::post('/publishers/{publisherId}/save', [PublishersController::class, 'save']);
        Route::post('/publishers/{publisherId}/delete', [PublishersController::class, 'delete']);

        Route::get('/territories', [TerritoriesController::class, 'index']);
        Route::post('/territories/filter', [TerritoriesController::class, 'filter']);
        Route::get('/available-territories', [TerritoriesController::class, 'availables']);
        Route::get('/territories/{territoryId}', [TerritoriesController::class, 'view']);
        Route::get('/territories-all/{territoryId}', [TerritoriesController::class, 'viewWithInactives']);
        Route::post('/territories/add', [TerritoriesController::class, 'add']);
        Route::post('/territories/{territoryId}/save', [TerritoriesController::class, 'save']);
        // Note: this endpoint may not be used
        Route::post('/territories/{territoryId?}', [TerritoriesController::class, 'save']);

        Route::post(
            '/territories/{territoryId}/addresses/edit/{addressId}', 
            [TerritoriesController::class, 'saveAddress']
        );
        Route::post(
            '/territories/{territoryId}/addresses/add', 
            [TerritoriesController::class, 'saveAddress']
        );
         // Note: Fallback for NG app endpoint (incorrect)
        Route::post('/addresses/remove/{addressId?}', [TerritoriesController::class, 'removeAddress']);
        Route::post('/addresses/{addressId}/remove', [TerritoriesController::class, 'removeAddress']);

        Route::post(
            '/territories/{territoryId}/notes/edit/{noteId}', 
            [TerritoriesController::class, 'saveNote']
        );
        Route::post(
            '/territories/{territoryId}/addresses/{addressId}/notes/add', 
            [TerritoriesController::class, 'addNote']
        );

        Route::get('/territories/{territoryId}/map', [TerritoriesController::class, 'map']);

        Route::get(
            '/territories-notes-activities', 
            [TerritoriesController::class, 'viewAllNotesActivities']
        );
        Route::get(
            '/territories/{territoryId}/activities', 
            [TerritoriesController::class, 'viewActivities']
        );
        Route::get('/all-activities', [TerritoriesController::class, 'viewAllActivities']);
    }
);