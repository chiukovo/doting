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

//main
Route::post('/message', 'AnimalCrossingController@message');

//web
Route::get('/animals/list', 'AnimalWebCrossingController@list');
Route::post('/animals/search', 'AnimalWebCrossingController@getAnimalSearch');
Route::get('/animals/getAllType', 'AnimalWebCrossingController@getAllType');

//test
Route::get('/test', 'AnimalCrossingController@index');

//爬蟲
Route::get('/getAnimalApi', 'ApiController@getAnimalApi');
Route::get('/getFishApi', 'ApiController@getFishApi');
Route::get('/getInsectApi', 'ApiController@getInsectApi');
Route::get('/getNewImg', 'ApiController@getNewImg');
Route::get('/getDiy', 'ApiController@getDiy');
Route::get('/apiItems', 'ApiController@getItems');
Route::get('/getClothes', 'ApiController@getClothes');
Route::get('/itemsToZh', 'ApiController@itemsToZh');
Route::get('/getAnimalHome', 'ApiController@getAnimalHome');
Route::get('/getAnimalCard', 'ApiController@getAnimalCard');