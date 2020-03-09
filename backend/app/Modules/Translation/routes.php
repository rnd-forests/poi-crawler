<?php

Route::group([
    'namespace' => 'Translation\Controllers',
//    'middleware' => ['auth:api'],
], function () {
    Route::get('trans/', 'TranslationController@trans');
    Route::get('trans/count-name-characters', 'TranslationController@countAllNameCharacters');
});
