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

Route::get('/', function () {
    return view('home');
});


// AngularJs Frontend UI
Route::get('/demo', function () {
   return view('spa2');
});


// Print PDF
Route::get('/pdf/{number?}/{nospace?}', 'PrintController@index');
Route::get('/pdf-html/{number?}', 'PrintController@template');


// Print Map
Route::get('/map/{number?}', 'PrintController@map');
Route::get('/map/{number?}/edit', 'PrintController@mapEdit');


// Route::get('/header-footer', 'PrintController@hf');

// output Territory
// Route::get('/output-territories/{number?}', 'PrintController@outputTerritory');

/*
// AngularJs Frontend Sample
Route::get('/front', function () {
   return view('spa');
});
*/



// API Endpoints
Route::group(['prefix' => 'v1'], function () {
	
	// Signup Endpoint
	Route::post('/signup', 'ApiController@signup');
	
	// Signin Endpoint
	Route::post('/signin', 'ApiController@signin');

	// Restricted Endpoint
	Route::get('/restricted', 'ApiController@restricted');
	
	// Restricted auth User Endpoint
	Route::get('/auth-user', 'ApiController@authUser');
	
	// Dashboard activities Endpoint
	Route::get('/activities', 'ApiController@activities');
	
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
	// Route::get('/publishers', 'PublishersController@add');
	// Route::get('/publishers', 'PublishersController@edit');
	// Route::get('/publishers', 'PublishersController@delete');
	
	// territories Endpoint
	Route::get('/territories', 'TerritoriesController@index');
	Route::post('/territories/filter', 'TerritoriesController@filter');
	Route::get('/available-territories', 'TerritoriesController@availables');
	Route::get('/territories/{territoryId}', 'TerritoriesController@view');
	Route::get('/territories-all/{territoryId}', 'TerritoriesController@viewAll');
	Route::post('/territories/add', 'TerritoriesController@add');
	Route::post('/territories/{territoryId?}', 'TerritoriesController@save');
	
	// territories addresses Endpoint
	Route::post('/territories/{territoryId}/addresses/edit/{addressId}', 'TerritoriesController@saveAddress');
	Route::post('/territories/{territoryId}/addresses/add', 'TerritoriesController@saveAddress');
	Route::post('/addresses/remove/{addressId?}', 'TerritoriesController@removeAddress');
	
	// territories notes Endpoint
	Route::post('/territories/{territoryId}/notes/edit/{noteId}', 'TerritoriesController@saveNote');
	Route::post('/territories/{territoryId}/addresses/{addressId}/notes/add', 'TerritoriesController@addNote');
   	
});


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
