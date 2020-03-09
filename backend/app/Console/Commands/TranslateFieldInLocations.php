<?php

namespace App\Console\Commands;

use App\Modules\Location\Jobs\TranslateFieldForLocation;
//use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
//use App\Modules\Location\Services\LocationService;
//use App\Modules\Translation\Services\GoogleTranslateCloudService;

class TranslateFieldInLocations extends Command
{
    /**
     * @var \App\Modules\Translation\Services\GoogleTranslateCloudService
     */
    protected $service;

    /**
     * @var \App\Modules\Location\Services\LocationService
     */
    protected $locationService;

//    /**
//     * @var Request
//     */
//    protected $request;

    protected $field = 'name';
    protected $table = 'locations';
    protected $target = 'en';

    /**
     * @var int price (in dollar) / 1M characters, see https://cloud.google.com/translate/pricing
     */
    protected $price = 20;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'location:translate-field';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Translate field name for all locations';

    /**
     * Create a new command instance.
     *
     * @param \App\Modules\Translation\Services\GoogleTranslateCloudService $service
     * @param \App\Modules\Location\Services\LocationService $locationService
     */
    public function __construct(
//        GoogleTranslateCloudService $service,
//        LocationService $locationService
    )
    {
        parent::__construct();

//        $this->service = $service;
//        $this->service->setTarget($this->target);
//
//        $this->locationService = $locationService;

//        $this->initRequest();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        return; // disable this command
        $data = DB::table('locations')
            ->whereNull('deleted_at')
            ->whereNull('locales')
            ->get([$this->field]);

        if (! $this->confirmProceed($data)) {
            $this->info('Aborted');

            return;
        }

        $count = 0;
        $sum = 0;

        foreach ($data as $e) {
//            try {
                $text = $e[$this->field];
                $sum += strlen($text);

                TranslateFieldForLocation::dispatch($e['_id']->__toString(), $text, $this->field);
//                $text = $this->service->trans($e[$this->field]);

//                $this->createOrUpdateTranslationForField($e['_id']->__toString(), $text);

                $count++;
//            } catch (\Exception $exception) {
//                \Log::error('Cannot translate location ' . $e['_id']->__toString() . ': ' . $exception->getMessage());
//            }
        }

        $this->info("$count document translated");
        $this->info("$sum characters translated");
        $this->info($this->computeTotalPrice($sum) . " dollar cost");
    }

    /**
     * @param $id
     * @param $text
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\BadRequestException
     */
//    protected function createOrUpdateTranslationForField($id, $text)
//    {
//        $this->locationService->createOrUpdateTranslation(
//            $id,
//            $this->makeRequest([$this->field => $text])
//        );
//    }

//    protected function makeRequest(array $params)
//    {
//        $this->request->merge($params);
//
//        return $this->request;
//    }

//    protected function initRequest()
//    {
//        $this->request = new Request();
//
//        $this->request->replace(['language' => $this->target]);
//    }

    /**
     * @param Collection $data
     * @return int
     */
    protected function computeTotalCharactersForField($data)
    {
        return $data->reduce(function ($sum, $e) {

            return $sum + strlen($e[$this->field]);
        }, 0);
    }

    protected function confirmProceed($data)
    {
        $sum = $this->computeTotalPrice($this->computeTotalCharactersForField($data));

        return $this->confirm("This action may cost \$$sum, do you want to continue?");
    }

    protected function computeTotalPrice($sum)
    {
        return $sum * $this->price / 1000000;
    }
}
