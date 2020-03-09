<?php

namespace App\Modules\Location\Jobs;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Modules\Location\Services\LocationService;
use App\Modules\Translation\Services\GoogleTranslateCloudService;

class TranslateFieldForLocation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $field;
    protected $target = 'en';

    protected $id, $text;


    /**
     * @var LocationService
     */
    protected $locationService;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var GoogleTranslateCloudService
     */
    protected $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id, $text, $field)
    {
        $this->id = $id;
        $this->text = $text;
        $this->field = $field;

        $this->service = app(GoogleTranslateCloudService::class);
        $this->service->setTarget($this->target);

        $this->locationService = app(LocationService::class);

        $this->initRequest();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $text = $this->service->trans($this->text);

            $this->createOrUpdateTranslationForField($this->id, $text);
        } catch (\Exception $e) {
            \Log::error('Cannot translate location ' . $this->id . ': ' . $e->getMessage());
        }
    }

    /**
     * @param $id
     * @param $text
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\BadRequestException
     */
    protected function createOrUpdateTranslationForField($id, $text)
    {
        $this->locationService->createOrUpdateTranslation(
            $id,
            $this->makeRequest([$this->field => $text])
        );
    }

    protected function makeRequest(array $params)
    {
        $this->request->merge($params);

        return $this->request;
    }

    protected function initRequest()
    {
        $this->request = new Request();

        $this->request->replace(['language' => $this->target]);
    }
}
