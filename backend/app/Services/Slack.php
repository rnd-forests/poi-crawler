<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 07/12/2018 15:33
 */

namespace App\Services;

/**
 * Slack Service
 * @link(Create Incoming Webhook, https://{workspace}.slack.com/apps/A0F7XDUAZ-incoming-webhooks)
 */
class Slack
{
    /**
     * Slack Webhook URL
     * @var string
     */
    protected $webhook_url;
    /**
     * Construct the class
     * @param string $webhook_url
     */
    public function __construct($webhook_url = null)
    {
        if (is_null($webhook_url)) {
            $webhook_url = config('services.slack.webhook');
        }

        $this->webhook_url = $webhook_url;
    }
    /**
     * Create static class
     * @param  string $webhook_url
     * @return self
     */
    public static function make($webhook_url = null)
    {
        return (new self($webhook_url));
    }
    /**
     * Get Webhook URL used
     * @return string
     */
    public function getWebhookUrl()
    {
        return $this->webhook_url;
    }
}
