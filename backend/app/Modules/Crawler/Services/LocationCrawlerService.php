<?php
/**
 * Created by Trang Ha Viet.
 * Contact: <viettrangha@gmail.com>
 * 08/10/2018 15:21
 */

namespace App\Modules\Crawler\Services;

use MongoDB\BSON\UTCDateTime;
use Illuminate\Support\Facades\DB;
use App\Modules\Crawler\Models\PendingJob;

class LocationCrawlerService
{
    public function crawlUrl($url, $user)
    {
        $this->addUrlToPendingList($url);

        $crawler = $this->getCrawlerForUrl($url);

        $this->dispatchJob($crawler, $url, $user);
    }

    public function getCrawlerForUrl($url)
    {
        return $this->getCrawler($this->extractDomain($url));
    }

    public function dispatchJob($crawler, $url, $user)
    {
        call_user_func($crawler . '::dispatch', $url, $user);
    }

    public function shouldCrawl($url)
    {
        $domain = $this->extractDomain($url);

        if (! $domain) return false;

        $crawler = $this->getCrawler($domain);

        return $crawler;
    }

    public function isSourceUrlExist($url)
    {
        return DB::table('locations')->where('source_url', '=', $url)->exists();
    }

    public function extractDomain($url)
    {
        $parsedResult = parse_url($url);

        if (! isset($parsedResult['host'])) {
            return false;
        }

        return $parsedResult['host'];
    }

    public function getCrawler($domain)
    {
        $allowSite = config('crawler.sources');

        if (! array_key_exists($domain, $allowSite)) {
            return false;
        }

        return $allowSite[$domain];
    }

    public function isUrlPending($url)
    {
//        return DB::table('pending_jobs')->where('url', 'like', "$url%")->exists();
        return DB::table(PendingJob::getTableName())->where('url', '=', $url)->exists();
    }

    public function addUrlToPendingList($url)
    {
        DB::table(PendingJob::getTableName())->insert(['url' => $url, 'created_at' => new UTCDateTime()]);
    }

    public function removePendingUrl($url)
    {
        DB::table(PendingJob::getTableName())->where('url', $url)->delete();
    }
}
