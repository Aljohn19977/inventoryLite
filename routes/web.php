<?php

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
    return view('admin.style.add');
});



Route::group(['namespace' => 'Admin'], function(){

    /* Style Management*/
    Route::resource('/style', 'StyleController');
    Route::get('/style/edit/{id}','StyleController@edit');
    Route::post('/apiGetAllStyle','StyleController@apiGetAllStyle')->name('api.getAllStyle');
    Route::post('/apiGetActiveStyle','StyleController@apiGetActiveStyle')->name('api.getActiveStyle');
    Route::post('/apiGetDeactiveStyle','StyleController@apiGetDeactiveStyle')->name('api.getDeactiveStyle');
    Route::get('/getSkuId', 'StyleController@getSkuId');
    Route::get('/getStyleInfo/{id}','StyleController@getStyleInfo');

    /* Brand Management*/
    Route::resource('/brand', 'BrandController');
    Route::get('/brand/edit/{id}','BrandController@edit');
    Route::get('/apiGetAllBrand','BrandController@apiGetAllBrand')->name('api.getAllBrand');

    /* Category Management*/
    Route::resource('/category', 'CategoryController');
    Route::get('/category/edit/{id}','CategoryController@edit');
    Route::get('/apiGetAllCategory','CategoryController@apiGetAllCategory')->name('api.getAllCategory');

});