<?php

namespace App\Modules\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class InvalidUrlNotification extends Notification
{
    use Queueable;

    public $url;

    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($url, $user)
    {
        $this->url = $url;

        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->error()
            ->from('Location CMS')
            ->content('Can not crawl url')
            ->attachment(function ($attachment) {
                $attachment->title($this->url)
                    ->content("{$this->user['name']} ({$this->user['email']})");
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
