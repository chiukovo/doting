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

//index
Route::get('/', function () {
  return view('index');
});

//update
Route::get('/update/version', function () {
  return view('update');
});

//update
Route::get('/instructions', function () {
  return view('instructions');
});

//家具服飾api
Route::post('/items/search', 'ItemsController@getItemsSearch');
Route::get('/items/getAllType', 'ItemsController@getAllType');


//家具服飾all
Route::get('/items/all/list', 'ItemsController@list')->name('all');

//家具
Route::get('/furniture/list', 'ItemsController@list')->name('furniture');

//服飾
Route::get('/apparel/list', 'ItemsController@list')->name('apparel');

//動物
Route::get('/animals/list', 'AnimalWebCrossingController@list');
Route::get('/animals/detail', 'AnimalWebCrossingController@detail');
Route::post('/animals/search', 'AnimalWebCrossingController@getAnimalSearch');
Route::get('/animals/getAllType', 'AnimalWebCrossingController@getAllType');

//npc
Route::get('/npc/list', 'AnimalWebCrossingController@list')->name('npc');

//博物館
Route::get('/museum/list', 'MuseumController@list');
Route::post('/museum/search', 'MuseumController@getMuseumSearch');

//魚
Route::get('/fish/list', 'FishController@list');
Route::post('/fish/search', 'FishController@getFishSearch');

//昆蟲
Route::get('/insect/list', 'InsectController@list');
Route::post('/insect/search', 'InsectController@getInsectSearch');

//Diy
Route::get('/diy/list', 'DiyController@list');
Route::post('/diy/search', 'DiyController@getDiySearch');

//test
//Route::get('/test', 'AnimalCrossingController@index');

//爬蟲
/*Route::get('/getAnimalApi', 'ApiController@getAnimalApi');
Route::get('/getFishApi', 'ApiController@getFishApi');
Route::get('/getInsectApi', 'ApiController@getInsectApi');
Route::get('/getAnimalDetail', 'ApiController@getAnimalDetail');
Route::get('/getAnimalEnWeb', 'ApiController@getAnimalEnWeb');
Route::get('/getDiy', 'ApiController@getDiy');
Route::get('/apiItems', 'ApiController@getItems');
Route::get('/getClothes', 'ApiController@getClothes');
Route::get('/itemsToZh', 'ApiController@itemsToZh');
Route::get('/getAnimalHome', 'ApiController@getAnimalHome');
Route::get('/getAnimalCard', 'ApiController@getAnimalCard');*/