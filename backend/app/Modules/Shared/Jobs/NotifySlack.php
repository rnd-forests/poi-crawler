<?php

namespace App\Modules\Shared\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Pressutto\LaravelSlack\Facades\Slack;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\SlackMessage;

class NotifySlack implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $timeout = 60;

    public $tries = 3;

    public $exception;

    /**
     * Create a new job instance.
     *
     * @param Exception $e
     */
    public function __construct(Exception $e)
    {
        $this->exception = $e;
    }

    public function retryUntil()
    {
        return now()->addSeconds(120);
    }

    /**
     * Execute the job.
     *
     * @param string $url
     * @return void
     */
    public function handle()
    {
        $message = exceptionToSlackMessage($this->exception);

        $this->notify($message);
    }

    protected function notify($message)
    {
        try {
            Slack::to('#logging')->send($message);
        } catch (\Exception $_) {}
    }
}
