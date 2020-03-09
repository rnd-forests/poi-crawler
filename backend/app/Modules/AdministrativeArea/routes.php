<?php

Route::group([
    'prefix' => 'areas',
    'namespace' => 'AdministrativeArea\Controllers',
//    'middleware' => ['auth:api']
], function () {
    Route::get('/', function () {
        return \App\Modules\AdministrativeArea\Models\Ward::where('name', 'like', "%tốt Động%")->get();
    });
    Route::group(['middleware' => 'admin'], function () {
        Route::post('/import/provinces', 'ImportController@importProvinces');
        Route::post('/import/districts', 'ImportController@importDistricts');
        Route::post('/import/wards', 'ImportController@importWards');
    });

    Route::get('/provinces', 'ProvinceController@index');
    Route::get('/provinces/{id}/districts', 'ProvinceController@districts');
    Route::get('/districts/{id}/wards', 'DistrictController@wards');
});
