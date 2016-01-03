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

// AngularJs Frontend Sample
Route::get('/front', function () {
   return view('spa');
});

// AngularJs Frontend Sample
Route::get('/front2', function () {
   return view('spa2');
});

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
	
	// publishers Endpoint
	Route::get('/publishers', 'PublishersController@index');
	Route::get('/publishers/{publisherId}', 'PublishersController@view');
	Route::post('/publishers/{publisherId?}', 'PublishersController@save');
	// Route::get('/publishers', 'PublishersController@add');
	// Route::get('/publishers', 'PublishersController@edit');
	// Route::get('/publishers', 'PublishersController@delete');
	
	// territories Endpoint
	Route::get('/territories', 'TerritoriesController@index');
	Route::get('/territories/{territoryId}', 'TerritoriesController@view');
	Route::post('/territories/{territoryId?}', 'TerritoriesController@save');
	
	// territories addresses Endpoint
	Route::post('/territories/{territoryId}/addresses/edit/{addressId}', 'TerritoriesController@saveAddress');
	Route::post('/territories/{territoryId}/addresses/add', 'TerritoriesController@saveAddress');
	Route::get('/addresses/remove/{addressId?}', 'TerritoriesController@removeAddress');
	
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
