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

//Route::get('/search', 'LineBotController@search');
//Route::get('/getApi', 'LineBotController@getApi');

//web
Route::get('/animals/list', 'AnimalWebCrossingController@list');
//動森
Route::post('/message', 'AnimalCrossingController@message');


//爬蟲
/*Route::get('/getAnimalApi', 'ApiController@getAnimalApi');
Route::get('/getFishApi', 'ApiController@getFishApi');
Route::get('/getInsectApi', 'ApiController@getInsectApi');
Route::get('/getNewImg', 'ApiController@getNewImg');
Route::get('/getDiy', 'ApiController@getDiy');
Route::get('/apiItems', 'ApiController@getItems');
Route::get('/getClothes', 'ApiController@getClothes');
Route::get('/itemsToZh', 'ApiController@itemsToZh');
Route::get('/getAnimalHome', 'ApiController@getAnimalHome');*/