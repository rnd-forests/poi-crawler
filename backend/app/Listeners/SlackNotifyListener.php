<?php

namespace App\Listeners;

use App\Events\SlackNotifyEvent;
use Illuminate\Queue\InteractsWithQueue;
use Pressutto\LaravelSlack\Facades\Slack;
use Illuminate\Contracts\Queue\ShouldQueue;

class SlackNotifyListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param  SlackNotifyEvent $event
     * @return void
     */
    public function handle(SlackNotifyEvent $event)
    {
//        if (config('logging.slack_error_notify')) {
            $this->notifySlack($event->channel, $event->message);
//        }
    }

    protected function notifySlack($channel, $message)
    {
        try {
            Slack::to($channel)->send($message);
        } catch (\Exception $_) {}
    }
}
