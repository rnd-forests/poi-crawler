<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;

class CacheApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh cache for config, cache, role-permission';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('cache:clear');
        $this->info('cache cleared');

        //        Artisan::call('route:clear');
        // NOTE: we can't cache route if it has any closure!
        // Artisan::call('route:cache');

        Artisan::call('config:cache');
        $this->info('-------------config refreshed------------');
    }
}
