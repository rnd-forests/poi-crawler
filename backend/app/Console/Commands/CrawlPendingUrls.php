<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\User\Models\User;
use App\Modules\Crawler\Models\PendingJob;
use App\Modules\Crawler\Services\LocationCrawlerService;

class CrawlPendingUrls extends Command
{
    protected $service;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:crawl-pending-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl all urls in pending jobs table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LocationCrawlerService $service)
    {
        $this->service = $service;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $urls = PendingJob::all();

        if ($urls->isEmpty()) {
            $this->info('no urls is in pending list');

            return;
        }

        $user = $this->getFirstAdmin();

        foreach ($urls as $url) {
            $crawler = $this->service->getCrawlerForUrl($url->url);

            $this->service->dispatchJob($crawler, $url->url, $user);
        }

        $this->info("{$urls->count()} url add to crawl queue");
    }

    protected function getFirstAdmin()
    {
        return User::firstAdmin(['_id', 'name', 'email']);
    }
}
