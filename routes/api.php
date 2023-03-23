<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['guest:api']], function () {
  Route::post('login', 'Auth\LoginController@login');
});

Route::group(['middleware' => ['jwt']], function () {
  Route::post('logout', 'Auth\LoginController@logout');

  Route::get('me', 'Auth\LoginController@me');
  // ----Users related api
  Route::prefix('/user')->group(function () {
    Route::get('/get-users', 'UserController@getUsers');
  }); 
  Route::prefix('/order')->group(function () {
    Route::get('/get-orders', 'OrdersController@getOrders');
    Route::post('/save-order', 'OrdersController@saveOrder');
    Route::post('/delete-order', 'OrdersController@deleteOrder');
  });
});
