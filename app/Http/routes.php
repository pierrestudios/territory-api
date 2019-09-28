<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get(
    '/', function () {
        return view('api-home');
    }
);

// API Docs
Route::get(
    '/docs', function () {
        $domain = isset($_SERVER['HTTP_HOST']) 
        ? $_SERVER['HTTP_HOST'] 
        : $_SERVER['SERVER_NAME'];
        return view('docs')->with('api_url', 'http://' . $domain . '/v1');
    }
);

// Print PDF
Route::get('/pdf/{number?}/{nospace?}', 'PrintController@index');
Route::get('/pdf-html/{number?}/{nospace?}', 'PrintController@template');

// Print S-13
Route::get('/s-13', 'PrintController@generateS13Pdf');

// Output CSV
Route::get('/csv/{number?}', 'PrintController@csv');

// Map with markers 
Route::get('/map/{number?}', 'PrintController@map');
Route::get('/map/{number?}/edit', 'PrintController@mapEdit');
Route::post('/map/{number?}/edit', 'PrintController@mapUpdate');

// Maps with boundaries
Route::get('/boundaries', 'PrintController@boundaryAll');
Route::get('/boundaries/{number?}/edit', 'PrintController@boundaryEdit');
Route::post('/boundaries/{number?}/edit', 'PrintController@boundaryUpdate');

// map Boundary and Markers Edit
Route::get('/map-boundaries/{number?}/edit', 'PrintController@mapBoundaryEdit');
Route::post('/map-boundaries/{number?}/edit', 'PrintController@mapBoundaryUpdate');

// map Markers Edit and Show Boundary 
Route::get('/map-markers/{number?}/edit', 'PrintController@mapMarkersEdit');
Route::post('/map-markers/{number?}/edit', 'PrintController@mapBoundaryUpdate');

// API Endpoints
Route::group(
    ['prefix' => 'v1', 'middleware' => 'cors'], function () {

        // Api info
        Route::get(
            '/', function () {
                return 'Territory Services API Version 1.0';
            }
        );

        // Signup Endpoint
        Route::post('/signup', 'ApiController@signup');

        // Signin Endpoint
        Route::post('/signin', 'ApiController@signin');

        // Restricted auth User Endpoint
        Route::get('/auth-user', 'ApiController@authUser');

        // Dashboard activities Endpoint
        Route::get('/activities', 'ApiController@activities');

        // Dashboard activities Endpoint
        Route::get('/validate', 'ApiController@validateServerURL');

        // publishers users Endpoint
        Route::get('/users', 'PublishersController@users');
        Route::post('/users/{userId}/save', 'PublishersController@saveUser');
        Route::post('/users/{userId}/delete', 'PublishersController@deleteUser');

        // publishers Endpoint
        Route::get('/publishers', 'PublishersController@index');
        Route::post('/publishers/filter', 'PublishersController@filter');
        Route::get('/publishers/{publisherId}', 'PublishersController@view');
        Route::post('/publishers/add', 'PublishersController@add');
        Route::post('/publishers/attach-user', 'PublishersController@attachUser');
        Route::post('/publishers/{publisherId}/save', 'PublishersController@save');
        Route::post('/publishers/{publisherId}/delete', 'PublishersController@delete');

        // territories Endpoint
        Route::get('/territories', 'TerritoriesController@index');
        Route::post('/territories/filter', 'TerritoriesController@filter');
        Route::get('/available-territories', 'TerritoriesController@availables');
        Route::get('/territories/{territoryId}', 'TerritoriesController@view');
        Route::get(
            '/territories-all/{territoryId}', 
            'TerritoriesController@viewWithInactives'
        );
        Route::post('/territories/add', 'TerritoriesController@add');
        Route::post('/territories/{territoryId}/save', 'TerritoriesController@save');
        // Note: this endpoint will be removed
        Route::post('/territories/{territoryId?}', 'TerritoriesController@save');

        // territories addresses Endpoint
        Route::post(
            '/territories/{territoryId}/addresses/edit/{addressId}', 
            'TerritoriesController@saveAddress'
        );
        Route::post(
            '/territories/{territoryId}/addresses/add', 
            'TerritoriesController@saveAddress'
        );
         // Note: app endpoint (incorrect)
        Route::post('/addresses/remove/{addressId?}', 'TerritoriesController@removeAddress');
        Route::post('/addresses/{addressId}/remove', 'TerritoriesController@removeAddress');

        // territories notes Endpoint
        Route::post(
            '/territories/{territoryId}/notes/edit/{noteId}', 
            'TerritoriesController@saveNote'
        );
        Route::post(
            '/territories/{territoryId}/addresses/{addressId}/notes/add', 
            'TerritoriesController@addNote'
        );

        // territories map Endpoint 
        Route::get('/territories/{territoryId}/map', 'TerritoriesController@map');

        // territory Activities Endpoint 
        Route::get(
            '/territories-notes-activities', 
            'TerritoriesController@viewAllNotesActivities'
        );
        Route::get(
            '/territories/{territoryId}/activities', 
            'TerritoriesController@viewActivities'
        );
        Route::get('/all-activities', 'TerritoriesController@viewAllActivities');

        // Password Reset
        Route::group(
            ['namespace' => 'Auth'], function () {
                Route::get(
                    '/password-reset/{lang}/{token?}', 
                    'PasswordController@getReset'
                );
                Route::post(
                    '/password-reset/{lang}', 
                    'PasswordController@postEmail'
                );
                // Remove to prevent TokenMismatch error, 'middleware' => 'web'
                Route::post(
                    '/password-retrieve/{lang?}', 
                    'PasswordController@postEmailApi'
                );
            }
        );
    }
);

// AngularJs App UI
Route::get(
    '/{lang?}', function ($lang = 'en') {
        try {
            $langPacks = File::get(
                resource_path(
                    'views/translation-all/lang-' . $lang . '.json'
                )
            );
        } catch (Exception $e) {
            return response(view('errors.404'), 404);
        }
        $Language = new App\Languages($langPacks, $lang);
        return view(
            'translation-all/index'
        )->with(
            'langPacks', $langPacks
        )->with(
            'Language', $Language
        )->with('lang', $lang);
    }
);

// NG App 
/***
 * Note: NG App is using this URL for password reset 
****/
Route::group(
    ['middleware' => ['web']], function () {
        Route::group(
            ['namespace' => 'Auth'], function () {
                Route::get(
                    '/password-reset/{lang}/{token?}', 
                    'PasswordController@getReset'
                );
                Route::post(
                    '/password-reset/{lang}', 
                    'PasswordController@postEmail'
                );
            }
        );
    }
);
