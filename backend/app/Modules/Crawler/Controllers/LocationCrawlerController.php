<?php

namespace App\Modules\Crawler\Controllers;

use App\Modules\Crawler\Requests\CreateCrawlingRequest;
use App\Modules\Crawler\Services\LocationCrawlerService;
use App\Modules\Shared\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class LocationCrawlerController extends Controller
{
    protected $service;

    /**
     * Create a new controller instance.
     *
     * @param LocationCrawlerService $service
     */
    public function __construct(LocationCrawlerService $service)
    {
        $this->service = $service;
    }

    public function store(CreateCrawlingRequest $request)
    {
        $url = $request->crawl_url;

        if ($this->service->isUrlPending($url)) return $this->resBadRequest('URL đã ở trong hàng đợi');

        if (! $this->service->getCrawler($this->service->extractDomain($url))) {
            $domains = implode(', ', array_keys(config('crawler.sources')));

            return $this->resBadRequest('Server chỉ hỗ trợ lấy thông tin từ website: ' . $domains);
        }

        if ($this->service->isSourceUrlExist($url)) {
            return $this->resBadRequest('URL đã được lấy');
        }

        $this->service->crawlUrl($url, auth()->user());

        return $this->resSuccess(null, 'URL đã được đẩy vào hàng đợi. Bạn hãy kiểm tra trong mục Locations');
    }
}
