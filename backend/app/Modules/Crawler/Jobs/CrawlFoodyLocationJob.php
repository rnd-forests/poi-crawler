<?php

namespace App\Modules\Crawler\Jobs;

//use App\Utils\Notify;
use App\Utils\Common;
use Illuminate\Support\Arr;
use Illuminate\Bus\Queueable;
use MongoDB\BSON\UTCDateTime;
use App\Events\SlackNotifyEvent;
use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use App\Utils\Location as LocationUtil;
use Illuminate\Queue\InteractsWithQueue;
use App\Modules\Location\Models\Location;
use Pressutto\LaravelSlack\Facades\Slack;
use Weidner\Goutte\GoutteFacade as Goutte;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Modules\Location\Models\LocationType;
use App\Modules\AdministrativeArea\Models\District;
use App\Modules\AdministrativeArea\Models\Province;
//use App\Modules\Notifications\InvalidUrlNotification;
use App\Modules\Translation\Services\TranslationService;
use App\Modules\Crawler\Services\LocationCrawlerService;

class CrawlFoodyLocationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 60;

    public $tries = 3;

    public $url;

    public $user;

    protected $crawlerService;

    /**
     * Create a new job instance.
     *
     * @param $url
     * @param $user
     */
    public function __construct($url, $user)
    {
        $this->url = $url;

        $this->crawlerService = app(LocationCrawlerService::class);

        if (method_exists($user, 'toArray')) {
            $this->user = $user->toArray();
        } else {
            $this->user = $user;
        }
    }

    public function retryUntil()
    {
        return now()->addSeconds(120);
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
//    public function failed(\Exception $e)
    public function failed()
    {
//        \Log::error("job failed with error {$e->getMessage()}");
        $this->removePendingUrl();
//        $this->notifyFailedJob($e);
    }

    /**
     * Execute the job.
     *
     * @param string $url
     * @return void
     */
    public function handle()
    {
        if ($this->crawlerService->isSourceUrlExist($this->url)) return;

        $doc = Goutte::request('GET', $this->url);

        // -----Get common info-----
        $commonInfo = $doc->filter('div.micro-header > div.main-information.disableSection > div > div')->first();

        if (! $commonInfo->count()) {
            $message = "Cannot parse URL {$this->url} by missing data, USER {$this->user['email']}";

//            Log::warning($message);

            $this->removePendingUrl();

//            $this->notifyFailedJob();
            $this->notifyInvalidUrl($message);

            return;
        }

        $geometry = $this->getCoordinate($doc);

        if (!$geometry || ! LocationUtil::validateLatLong($geometry[1], $geometry[0])) {
            $this->removePendingUrl();

            return;
        }

        $name = $this->getContentSelector('div.main-info-title > h1', $commonInfo);

        $footerCommonInfo = $commonInfo->filter('div.disableSection')->first();

        $addressNode = $footerCommonInfo->filter('div:nth-child(1)')->first();

        $province = $this->getContentSelector('div > span:nth-child(5)', $addressNode);

        $district = $this->getContentSelector('div > span:nth-child(4) > a > span', $addressNode);

        $address = $this->getContentSelector('div > span:nth-child(2) > a > span', $addressNode);

        $formatted_address = implode(', ', [$name, $address, $district, $province]);

        $dbProvince = Province::findNameLike($province);
        $dbDistrict = District::findNameLike($district);

        $priceRange = $this->getContentSelector('div.res-common-price > div.res-common-minmaxprice > span:nth-child(2) > span', $footerCommonInfo);

        $categories = $this->getCategories($commonInfo);

        $categories = $this->findOrCreateCategories($categories);

        $now = new UTCDateTime();

        $location = [
            'name' => $name,
            'avatar' => $this->getAvatar($doc),
            'description' => $this->getMetaContent($doc, 'description'),
            'keywords' => $this->getMetaContent($doc, 'keywords'),
            'slug' => $this->getSlugFromUrl($this->url),
            'area' => [
                'province' => $dbProvince ? $dbProvince->toArray() : ['name' => $province],
                'district' => $dbDistrict ? $dbDistrict->toArray() : ['name' => $district],
                'ward' => null,
//                'address' => $address,
            ],
            'formatted_address' => $formatted_address,
            'price_range' => $priceRange,
            'type' => $categories,
            'source' => 'Foody.vn',
            'source_url' => $this->url,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $translateName = $this->translate($name);

        if ($translateName) {
            $location['locales'] = [
                [
                    'name' => $translateName,
                    'language' => 'en',
                ]
            ];
        }

        if ($priceRange) {
            $location['price_range'] = $priceRange;
        }

        $review = $this->getReview($commonInfo);

        if ($review) {
            $location['review'] = $review;
        }

        $location['geometry'] = Location::getGeometryFormat($geometry);

        $location['edited_by'] = [Common::getEditedUser($this->user)];

        $this->removePendingUrl();

        DB::table('locations')->insert($location);
    }

    protected function translate($text)
    {
        try {
            return app(TranslationService::class)->ensureTrans($text);
        } catch (\Exception $_) {
            return null;
        }
    }

    protected function notifyInvalidUrl($message)
    {
//        (new Notify(config('services.slack.webhook_invalid_urls')))->notify(new InvalidUrlNotification($this->url, $this->user));
        try {
            Slack::to('#invalid_urls')->send($message);
        } catch (\Exception $_) {}
    }

    protected function notifyFailedJob(\Exception $e)
    {
        event(new SlackNotifyEvent(exceptionToSlackMessage($e)));
//        Mail::to(explode(',', config('mail.admins')))
//            ->sendNow(new FailedCrawlJob($this->url, $this->user));
    }

    protected function getCategories($node)
    {
        return $node->filter('div.main-info-title > div.category > div.category-items > a')->each(function ($e) {
//            return trim($e->text());
            return [
                'name' => trim($e->text(), " \t\n\r\0\x0B,"),
                'slug' => $this->getSlugFromUrl($e->attr('href')),
            ];
        });
    }

    protected function findOrCreateCategories($categories)
    {
        return array_map(function ($e) {
            $type = LocationType::firstOrCreate(['slug' => $e['slug']], ['name' => $e['name']]);

            return Arr::only($type->toArray(), ['_id', 'name', 'slug']);
        }, $categories);
    }

    protected function getCoordinate($doc)
    {
        try {
            $src = $doc->filter('div.micro-right1000 > section > div > div > div > div > div.micro-left > div > div.microsite-res-mapfacilities > div.micro-home-intro.disableSection > div:nth-child(1) > div.microsite-map > div:nth-child(2) > a > img')->first()->attr('src');
        } catch (\Exception $e) {
            return null;
        }

        $data = explode('_', basename(parse_url($src, PHP_URL_PATH)));

        $longitude = $data[2];
        $longitude = substr($longitude, 0, strpos($longitude, '.'));
        $longitude = str_replace('-', '.', $longitude);

        return [$longitude, str_replace('-', '.', $data[1])];
    }

    protected function getAvatar($doc)
    {
        return $doc->filter('div.micro-header > div.main-image > div img')->first()->attr('src');
    }

    protected function getImageSrc($doc, $selector)
    {
        return $doc->filter($selector)->first()->attr('src');
    }

    protected function getMetaContent($doc, $name)
    {
        return trim($doc->filter("head > meta[name=\"{$name}\"]")->first()->attr('content'));
    }

    protected function getSlugFromUrl($url)
    {
        return basename(parse_url($url, PHP_URL_PATH));
    }

    protected function getReview($doc)
    {
        $reviewNode = $doc->filter('#res-summary-point > div')->first();

        $total = (int) trim($reviewNode->filter('div.microsite-top-points-block > div.microsite-review-count')->first()->text());

        // if there are no review
        if ($total === 0) {
            return null;
        }

        $fields = [];

        $reviewNode->filter('div.microsite-point-group > div')->each(function ($node) use (&$fields) {
//            $fields[$this->getContent($node, 'div.label')] = $this->getContent($node, 'div:nth-child(1) > span');
            array_push($fields, [
                'name' => $this->getContentSelector('div.label', $node),
                'value' => $this->getContentSelector('div:nth-child(1) > span', $node),
            ]);
        });

        $avgRating = trim($reviewNode->filter('div.microsite-top-points-block > div.microsite-point-avg')->first()->text());

        return [
            'fields' => $fields,
            'avg' => $avgRating,
            'total' => $total,
        ];
    }

    protected function getContentSelector($selector, $node)
    {
        try {
            return trim($node->filter($selector)->first()->text());
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function removePendingUrl()
    {
        $this->crawlerService->removePendingUrl($this->url);
    }
}
