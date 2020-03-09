<?php

Route::group([
    'namespace' => 'Location\Controllers',
//    'middleware' => ['auth:api'],
], function () {
    Route::group(['prefix' => 'locations'], function () {
        Route::get('/', 'LocationController@index');
    });

    Route::group([
        'middleware' => ['auth:api'],
    ], function () {
        Route::group([
            'prefix' => 'location-types',
//        'middleware' => ['admin']
        ], function () {
            Route::get('/', 'LocationTypeController@index');
            Route::post('/', 'LocationTypeController@store')->middleware('admin');
            Route::get('/{id}', 'LocationTypeController@show');
            Route::put('/{id}', 'LocationTypeController@update')->middleware('admin');

            Route::get('/{id}/locales/{locale}', 'LocationTypeController@getTranslation')->middleware('admin');
//        Route::post('/{id}/locales', 'LocationTypeController@createTranslation')->middleware('admin');
//        Route::patch('/{id}/locales', 'LocationTypeController@updateTranslation')->middleware('admin');
            // temporary disable delete api
//        Route::delete('/{id}', 'LocationTypeController@delete')->middleware('admin');
        });

//        Route::resource('locations', 'LocationController');
        Route::group(['prefix' => 'locations'], function () {
//            Route::get('/', 'LocationController@index');
            Route::post('/', 'LocationController@store');
            Route::get('/{id}', 'LocationController@show');
            Route::put('/{id}', 'LocationController@update');
            Route::delete('/{id}', 'LocationController@destroy');
            Route::put('/', 'LocationController@bulkUpdate')->middleware('admin');
            Route::get('/{id}/locales/{locale}', 'LocationController@getTranslation')->middleware('admin');
        });

        Route::post('detect-location', 'LocationController@detect');
    });

    Route::group(['prefix' => 'v1', 'middleware' => [
        'throttle:500,1',
//        'auth:api',
//        'scopes:locations-read',
//        'client',
    ]], function () {
        Route::group(['prefix' => 'locations'], function () {
            Route::get('/search', 'LocationController@search');
            Route::post('/search/groups', 'LocationController@searchByGroup');
        });

        Route::group(['prefix' => 'location-types'], function () {
            Route::get('/', 'LocationTypeController@index');
        });
    });
});
