<?php

Route::group([
    'prefix' => 'auth',
    'namespace' => 'Auth\Controllers',
], function () {
    Route::post('sign-in', 'SignInController@signIn');

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('me', 'AuthController@me');
        Route::post('sign-out', 'AuthController@signOut');
    });
});
