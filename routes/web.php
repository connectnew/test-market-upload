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
    return view('home');
});

Route::group(
    [
        'namespace' => 'Api\V1',
        'name' => 'api.v1.',
        'prefix' => 'api/v1',
        'as' => 'api.v1.',
        'middleware' => ['web'],
    ],
    function () {
        Route::post('import/upload', 'ImportController@upload')->name('import.upload');
        Route::post('import/parse', 'ImportController@parse')->name('import.parse');
        Route::get('import/test', 'ImportController@test')->name('import.test');
    }
);
