<?php

Route::group([
    'namespace' => 'Crawler\Controllers',
    'middleware' => ['auth:api'],
    'prefix' => 'crawlers',
], function () {
    Route::group([
        'prefix' => '/locations',
    ], function () {
        Route::post('/', 'LocationCrawlerController@store');
    });

    Route::get('/crawl-failed', function () {
        Artisan::queue('location:re-crawl-failed-urls');

        return 'Success';
    });

    Route::get('/crawl-pending', function () {
        Artisan::queue('location:crawl-pending-urls');

        return 'Success';
    });
});
