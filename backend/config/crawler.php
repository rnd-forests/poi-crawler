<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 09/10/2018 14:10
 */

return [
    'sources' => [
        'www.foody.vn' => \App\Modules\Crawler\Jobs\CrawlFoodyLocationJob::class,
    ],

    'GOOGLE_MAP_KEY' => env('GOOGLE_MAP_KEY'),

    'default_language' => 'vi',
];
