<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordController;

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

// Note: NG App is using this URL for password reset 
Route::group(['namespace' => 'Auth'], function () {
    Route::get(
        '/password-reset/{lang}/{token?}',
        [PasswordController::class, 'getReset']
    );
    Route::post(
        '/password-reset/{lang}',
        [PasswordController::class, 'postEmail']
    );
});

// AngularJs App UI
Route::fallback(function ($lang = 'en') {
    try {
        $langPacks = File::get(
            resource_path(
                'views/translation-all/lang-' . $lang . '.json'
            )
        );
    } catch (Exception $e) {
        return response(view('errors.404'), 404);
    }
    
    $Language = new App\Models\Languages($langPacks, $lang);
    return view(
        'translation-all/index'
    )->with(
        'langPacks', $langPacks
    )->with(
        'Language', $Language
    )->with('lang', $lang);
});
