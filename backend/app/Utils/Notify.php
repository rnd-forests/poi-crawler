<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 07/12/2018 15:40
 */

namespace App\Utils;

use Illuminate\Notifications\Notifiable;

class Notify
{
    use Notifiable;

    protected $webhook;

    public function __construct($webhook = null)
    {
        $this->webhook = $webhook;
    }

    public function routeNotificationForSlack()
    {
        return slack($this->webhook)->getWebhookUrl();
    }
}
