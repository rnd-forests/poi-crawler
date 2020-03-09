<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Notifications\Messages\SlackMessage;

class SlackNotifyEvent
{
    use Dispatchable, InteractsWithSockets;

    public $channel;

    public $message;

    /**
     * Create a new event instance.
     *
     * @param SlackMessage|string $message
     * @return void
     */
    public function __construct($message, $channel = '#logging')
    {
        $this->channel = $channel;

        $this->message = $message;
    }
}
