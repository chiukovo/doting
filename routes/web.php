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
Route::group(['prefix' => 'filemanager', 'middleware' => []], function () {
	\UniSharp\LaravelFilemanager\Lfm::routes();
});

//main
Route::post('/message', 'AnimalCrossingController@message');

//statistics
Route::get('/statistics', 'AnimalWebCrossingController@statistics');
Route::post('/statistics/getData', 'AnimalWebCrossingController@statisticsGetData');

//donate
Route::get('/donate', function () {

	$donate = DB::table('donate')
	    ->orderBy('id', 'desc')
	    ->get()
	    ->toArray();

  return view('donate', [
  	'donate' => $donate
  ]);
});

//index
Route::get('/', 'IndexController@index');
Route::post('/indexData', 'IndexController@indexData');

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

//植物
Route::get('/plant/list', 'ItemsController@list')->name('plant');

//動物
Route::get('/animals/list', 'AnimalWebCrossingController@list');
Route::get('/animals/detail', 'AnimalWebCrossingController@detail');
Route::post('/animals/search', 'AnimalWebCrossingController@getAnimalSearch');
Route::get('/animals/getAllType', 'AnimalWebCrossingController@getAllType');

//相容度分析
Route::get('/animals/compatible', 'AnimalWebCrossingController@compatible')->name('analysis');
Route::post('/animals/getAnimalsGroupRace', 'AnimalWebCrossingController@getAnimalsGroupRace');
Route::get('/animals/analysis', 'AnimalWebCrossingController@analysis');

//npc
Route::get('/npc/list', 'AnimalWebCrossingController@list')->name('npc');

//博物館
Route::get('/museum/list', 'MuseumController@list')->name('museum');
Route::post('/museum/search', 'MuseumController@getMuseumSearch');

//畫
Route::get('/art/list', 'ArtController@list')->name('art');
Route::get('/art/detail', 'ArtController@detail');
Route::post('/art/search', 'ArtController@getArtSearch');

//魚
Route::get('/fish/list', 'FishController@list')->name('fish');
Route::post('/fish/search', 'FishController@getFishSearch');

//昆蟲
Route::get('/insect/list', 'InsectController@list')->name('insect');
Route::post('/insect/search', 'InsectController@getInsectSearch');

//化石
Route::get('/fossil/list', 'FossilController@list')->name('fossil');
Route::post('/fossil/search', 'FossilController@getFossilSearch');

//Diy
Route::get('/diy/list', 'DiyController@list');
Route::post('/diy/search', 'DiyController@getDiySearch');

//kk
Route::get('/kk/list', 'KKController@list')->name('kk');
Route::post('/kk/search', 'KKController@getKKSearch');
Route::get('/kk/detail', 'KKController@detail');

//test
Route::get('/test', 'AnimalCrossingController@index');

//爬蟲
/*Route::get('/getKKZhName', 'ApiController@getKKZhName');
Route::get('/getNewFurniture', 'ApiController@getNewFurniture');
Route::get('/getAnimalIcon', 'ApiController@getAnimalIcon');
Route::get('/getAnimalApi', 'ApiController@getAnimalApi');
Route::get('/getFishApi', 'ApiController@getFishApi');
Route::get('/getInsectApi', 'ApiController@getInsectApi');
Route::get('/getAnimalDetail', 'ApiController@getAnimalDetail');
Route::get('/getAnimalEnWeb', 'ApiController@getAnimalEnWeb');
Route::get('/getDiy', 'ApiController@getDiy');
Route::get('/apiItems', 'ApiController@getItems');
Route::get('/getClothes', 'ApiController@getClothes');
Route::get('/itemsToZh', 'ApiController@itemsToZh');
Route::get('/getAnimalHome', 'ApiController@getAnimalHome');
Route::get('/getAnimalCard', 'ApiController@getAnimalCard');
Route::get('/getArtwork', 'ApiController@getArtwork');
Route::get('/getFossil', 'ApiController@getFossil');
Route::get('/getPlant', 'ApiController@getPlant');
Route::get('/getKK', 'ApiController@getKK');*/



//admin
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function() {
	Route::get('/', function () {
	  return view('admin.index');
	});
	Route::get('/login', function () {
	  return view('admin.login');
	});
});