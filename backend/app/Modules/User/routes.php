<?php

Route::group([
    'prefix' => 'users',
    'namespace' => 'User\Controllers',
    'middleware' => ['auth:api']
], function () {
    Route::get('/', 'UserController@index');
    Route::post('/', 'UserController@store')->middleware('admin');
    Route::get('/{id}', 'UserController@show');
    Route::put('/{id}', 'UserController@update')->middleware('admin');
    Route::delete('/{id}', 'UserController@delete')->middleware('admin');
});
