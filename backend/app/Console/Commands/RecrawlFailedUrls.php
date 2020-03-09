<?php

namespace App\Console\Commands;

use App\Modules\Crawler\Jobs\CrawlFoodyLocationJob;
use App\Modules\Crawler\Models\FailedJob;
use Illuminate\Console\Command;
use App\Modules\Crawler\Services\LocationCrawlerService;
use Illuminate\Support\Facades\DB;

class ReCrawlFailedUrls extends Command
{
    protected $service;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:re-crawl-failed-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-crawl failed urls';

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
     * @return mixed | void
     */
    public function handle()
    {
        $jobs = DB::table(FailedJob::getTableName())->get(['_id', 'payload']);

        $count = 0;

        foreach ($jobs as $e) {
            $job = $this->decodeJob($e);
            $id = $e['_id']->__toString();

            if (
                ! $job instanceof CrawlFoodyLocationJob ||
                $this->service->isUrlPending($job->url)
            ) {
                $this->removeFailedJob($id);

                continue;
            }

            $count++;

            try {
                $this->service->crawlUrl($job->url, $job->user);
                $this->removeFailedJob($id);
            } catch (\Exception $e) {
                \Log::info("fail $id, {$job->url}");
            }

        }

        $this->info("$count urls added to queue");
    }

    protected function removeFailedJob($id)
    {
        return DB::table(FailedJob::getTableName())->delete($id);
    }

    protected function decodeJob($job)
    {
        $job = json_decode($job['payload'], true);

        return unserialize($job['data']['command']);
    }
}
