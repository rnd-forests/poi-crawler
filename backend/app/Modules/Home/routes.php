<?php

//Route::get('/', function () {
//    return 'Ping OK';
//});
Route::get('/', 'Home\Controllers\HomeController@index');
