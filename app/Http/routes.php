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
    return view('api-home');
});

// API Docs
Route::get('/docs', function () {
	$domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
	return view('docs')->with('api_url', 'http://'. $domain . '/v1');
});

// Print PDF
Route::get('/pdf/{number?}/{nospace?}', 'PrintController@index');
Route::get('/pdf-html/{number?}/{nospace?}', 'PrintController@template');

// Map with markers 
Route::get('/map/{number?}', 'PrintController@map');
Route::get('/map/{number?}/edit', 'PrintController@mapEdit');
Route::post('/map/{number?}/edit', 'PrintController@mapUpdate');

// Maps with boundaries
Route::get('/boundaries', 'PrintController@boundaryAll');
Route::get('/boundaries/{number?}/edit', 'PrintController@boundaryEdit');
Route::post('/boundaries/{number?}/edit', 'PrintController@boundaryUpdate');

// API Endpoints
Route::group(['prefix' => 'v1', 'middleware' => 'cors'], function () {
	
	// Signup Endpoint
	Route::post('/signup', 'ApiController@signup');
	
	// Signin Endpoint
	Route::post('/signin', 'ApiController@signin');
	
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
	Route::post('/addresses/remove/{addressId?}', 'TerritoriesController@removeAddress'); // creole app endpoint (incorrect)
	Route::post('/addresses/{addressId}/remove', 'TerritoriesController@removeAddress');
	
	// territories notes Endpoint
	Route::post('/territories/{territoryId}/notes/edit/{noteId}', 'TerritoriesController@saveNote');
	Route::post('/territories/{territoryId}/addresses/{addressId}/notes/add', 'TerritoriesController@addNote');
	
	// territories map Endpoint 
	Route::get('/territories/{territoryId}/map', 'TerritoriesController@map');
	
	// territory Activities Endpoint 
	Route::get('/territories-notes-activities', 'TerritoriesController@viewAllNotesActivities');
	Route::get('/territories/{territoryId}/activities', 'TerritoriesController@viewActivities');
	Route::get('/all-activities', 'TerritoriesController@viewAllActivities');

   	// Password Reset
   	Route::group(['namespace' => 'Auth', 'middleware' => 'web'], function() {
		Route::get('/password-reset/{lang}/{token?}', 'PasswordController@getReset');	
		Route::post('/password-reset/{lang}', 'PasswordController@postEmail');	
	});
});


// AngularJs App UI

Route::get('/{lang?}', function ($lang='en') {
	try {
		$langPacks = File::get(resource_path('views/translation-all/lang-'.$lang.'.json'));
	} catch (Exception $e) {
		return view('errors/404');
	}
	$Language = new App\Languages($langPacks, $lang);
	return view('translation-all/index')->with('langPacks', $langPacks)->with('Language', $Language)->with('lang', $lang);
});


